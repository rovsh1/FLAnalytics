!function (t) {
    function e(a) {
        if (n[a]) return n[a].exports;
        var r = n[a] = {i: a, l: !1, exports: {}};
        return t[a].call(r.exports, r, r.exports, e), r.l = !0, r.exports
    }

    var n = {};
    e.m = t, e.c = n, e.i = function (t) {
        return t
    }, e.d = function (t, n, a) {
        e.o(t, n) || Object.defineProperty(t, n, {configurable: !1, enumerable: !0, get: a})
    }, e.n = function (t) {
        var n = t && t.__esModule ? function () {
            return t.default
        } : function () {
            return t
        };
        return e.d(n, "a", n), n
    }, e.o = function (t, e) {
        return Object.prototype.hasOwnProperty.call(t, e)
    }, e.p = "", e(e.s = 3)
}({
    3: function (t, e) {
        gapi.analytics.ready(function () {
            function t(t) {
                if (a.test(t)) return t;
                var r = n.exec(t);
                if (r) return e(+r[1]);
                if ("today" == t) return e(0);
                if ("yesterday" == t) return e(1);
                throw new Error("Cannot convert date " + t)
            }

            function e(t) {
                var e = new Date;
                e.setDate(e.getDate() - t);
                var n = String(e.getMonth() + 1);
                n = 1 == n.length ? "0" + n : n;
                var a = String(e.getDate());
                return a = 1 == a.length ? "0" + a : a, e.getFullYear() + "-" + n + "-" + a
            }

            var n = /(\d+)daysAgo/, a = /\d{4}\-\d{2}\-\d{2}/;
            gapi.analytics.createComponent("DateRangeSelector", {
                execute: function () {
                    var e = this.get();
                    e["start-date"] = e["start-date"] || "7daysAgo", e["end-date"] = e["end-date"] || "yesterday", this.container = "string" == typeof e.container ? document.getElementById(e.container) : e.container, e.template && (this.template = e.template), this.container.innerHTML = this.template;
                    var n = this.container.querySelectorAll("input");
                    return this.startDateInput = n[0], this.startDateInput.value = t(e["start-date"]), this.endDateInput = n[1], this.endDateInput.value = t(e["end-date"]), this.setValues(), this.setMinMax(), this.container.onchange = this.onChange.bind(this), this
                },
                onChange: function () {
                        this.setValues(),
                        // this.setMinMax(), // fix date selector
                        this.emit("change", {
                        "start-date": this["start-date"],
                        "end-date": this["end-date"]
                    })
                },
                setValues: function () {
                    this["start-date"] = this.startDateInput.value, this["end-date"] = this.endDateInput.value
                },
                setMinMax: function () {
                    this.startDateInput.max = this.endDateInput.value, this.endDateInput.min = this.startDateInput.value
                },
                template: '<div class="DateRangeSelector">  <div class="DateRangeSelector-item">    <label>Start Date</label>     <input type="date">  </div>  <div class="DateRangeSelector-item">    <label>End Date</label>     <input type="date">  </div></div>'
            })
        })
    }
});