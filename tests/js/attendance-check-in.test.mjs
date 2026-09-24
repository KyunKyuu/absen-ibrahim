import test from 'node:test';
import assert from 'node:assert/strict';
import { checkPosition, mountAttendance } from '../../public/js/attendance-check-in.js';

const settings = { latitude: -6.2, longitude: 106.8, accuracy: 30, radius: 100 };
const fix = (coords = {}, timestamp = Date.now()) => ({
    coords: { latitude: -6.2, longitude: 106.8, accuracy: 10, ...coords }, timestamp,
});

function harness({ secure = true, supported = true, online = true } = {}) {
    const element = () => ({ listeners: {}, disabled: true, hidden: true, value: '', textContent: '',
        addEventListener(name, callback) { this.listeners[name] = callback; }, setAttribute() {} });
    const elements = { 'geo-button': element(), 'geo-cancel': element(), 'geo-status': element() };
    const fields = Object.fromEntries(['latitude', 'longitude', 'accuracy', 'location_timestamp'].map(name => [name, element()]));
    const form = { ...element(), dataset: settings, elements: { namedItem: name => fields[name] } };
    const doc = { ...element(), hidden: false, getElementById: id => elements[id] };
    const state = { sends: 0, cleared: [], timers: new Map(), watches: 0 };
    const browser = { ...element(), isSecureContext: secure,
        setTimeout(callback) { state.timers.set(1, callback); return 1; },
        clearTimeout(id) { state.timers.delete(id); },
        HTMLFormElement: { prototype: { submit() { state.sends++; } } },
    };
    const nav = { onLine: online, geolocation: supported ? {
        watchPosition(success, failure, options) {
            state.success = success; state.failure = failure; state.options = options; state.watches++; return state.watches;
        },
        clearWatch(id) { state.cleared.push(id); },
    } : undefined };
    mountAttendance(form, browser, nav, doc);
    return { state, fields, elements, doc, browser, nav,
        submit: () => form.listeners.submit({ preventDefault() {} }),
    };
}

test('fresh, accurate GPS at school passes; stale, invalid and out-of-radius fixes do not', () => {
    assert.equal(checkPosition(fix(), settings).distance, 0);
    for (const position of [fix({}, Date.now() - 16000), fix({}, Date.now() + 11000),
        fix({ accuracy: 0 }), fix({ accuracy: 31 }), fix({ latitude: NaN }),
        fix({ latitude: -6.3 }), fix({ latitude: -6.1993, accuracy: 25 })]) {
        assert.ok(checkPosition(position, settings).error);
    }
});

test('insecure page, unsupported browser and offline state cannot submit attendance', () => {
    for (const options of [{ secure: false }, { supported: false }, { online: false }]) {
        const h = harness(options);
        h.submit();
        assert.equal(h.state.watches, 0);
        assert.equal(h.state.sends, 0);
        assert.ok(h.elements['geo-status'].textContent);
    }
});

test('permission denied, unavailable GPS and timeout never submit; user may retry', () => {
    for (const code of [1, 2, 3]) {
        const h = harness();
        h.submit();
        h.state.failure({ code });
        assert.equal(h.state.sends, 0);
        assert.equal(h.elements['geo-button'].disabled, false);
        assert.deepEqual(h.state.cleared, [1]);
        h.submit();
        assert.equal(h.state.watches, 2);
    }
});

test('waits for precision and submits only once, cleaning up the GPS watcher', () => {
    const h = harness();
    h.submit();
    h.submit();
    assert.equal(h.state.watches, 1);
    assert.deepEqual(h.state.options, { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 });
    h.state.success(fix({ accuracy: 200 }));
    assert.equal(h.state.sends, 0);
    const position = fix();
    h.state.success(position);
    h.state.success(position);
    h.submit();
    assert.equal(h.state.sends, 1);
    assert.deepEqual(h.state.cleared, [1]);
    assert.equal(h.state.timers.size, 0);
    assert.equal(h.fields.location_timestamp.value, position.timestamp);
    assert.equal(h.fields.latitude.value, -6.2);
});

test('unacceptable fixes reach a bounded timeout without submitting', () => {
    const h = harness();
    h.submit();
    h.state.success(fix({ latitude: -6.3 }));
    h.state.timers.get(1)();
    h.state.success(fix());
    assert.equal(h.state.sends, 0);
    assert.match(h.elements['geo-status'].textContent, /di luar radius/);
});

test('leaving the page or cancelling stops GPS and ignores old callbacks', () => {
    for (const action of ['hide', 'cancel']) {
        const h = harness();
        h.submit();
        const oldSuccess = h.state.success;
        if (action === 'hide') {
            h.doc.hidden = true;
            h.doc.listeners.visibilitychange();
            h.doc.hidden = false;
        } else {
            h.elements['geo-cancel'].listeners.click();
        }
        h.submit();
        oldSuccess(fix());
        assert.equal(h.state.sends, 0);
        h.state.success(fix());
        assert.equal(h.state.sends, 1);
    }
});
