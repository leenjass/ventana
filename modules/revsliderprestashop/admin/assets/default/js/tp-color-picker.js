/**************************************************************************
 * tp-color-picker.js - Color Picker Plugin for Revolution Slider
 * @version: 1.0.4 (7.9.2017)
 * @author ThemePunch
 **************************************************************************/

window.RevColor = {
  defaultValue: "#ffffff",
  isColor: /(^#[0-9A-F]{6}$)|(^#[0-9A-F]{3}$)/i,
  get: function(e) {
      return e ? RevColor.process(e, !0)[0] : "transparent"
  },
  parse: function(e, r, a) {
      e = RevColor.process(e, !0);
      var t = [];
      return t[0] = r ? r + ": " + e[0] + ";" : e[0], a && (t[1] = e[1]), t
  },
  convert: function(e, r) {
      if (!e || "string" != typeof e) return RevColor.defaultValue;
      if ("transparent" === e) return e;
      if (-1 !== e.search(/\[\{/) || -1 !== e.search("gradient")) return RevColor.process(e, !0)[0];
      if (void 0 === r || isNaN(r)) return RevColor.process(e, !0)[0];
      if ((r = parseFloat(r)) <= 1 && (r *= 100), 0 === (r = Math.max(Math.min(parseInt(r, 10), 100), 0))) return "transparent";
      try {
          return -1 !== e.search("#") || e.length < 8 ? (RevColor.isColor.test(e) || (e = e.replace(/[^A-Za-z0-9#]/g, "")), RevColor.processRgba(RevColor.sanitizeHex(e), r)) : (e = RevColor.rgbValues(e, 3), RevColor.rgbaString(e[0], e[1], e[2], .01 * r))
      } catch (e) {
          return RevColor.defaultValue
      }
  },
  process: function(e, r) {
      if ("string" != typeof e) return r && (e = RevColor.sanitizeGradient(e)), [RevColor.processGradient(e), "gradient", e];
      if ("transparent" === e.trim()) return ["transparent", "transparent"];
      if (-1 === e.search(/\[\{/)) return -1 !== e.search("#") ? [RevColor.sanitizeHex(e), "hex"] : -1 !== e.search("rgba") ? [e.replace(/\s/g, "").replace(/false/g, "1"), "rgba"] : -1 !== e.search("rgb") ? [e.replace(/\s/g, ""), "rgb"] : RevColor.isColor.test(e) ? [e, "hex"] : ["transparent", "transparent", !0];
      try {
          return e = JSON.parse(e.replace(/\&/g, '"')), r && (e = RevColor.sanitizeGradient(e)), [RevColor.processGradient(e), "gradient", e]
      } catch (e) {
          return console.log("RevColor.process() failed to parse JSON string"), ["linear-gradient(0deg, rgba(255, 255, 255, 1) 0%, rgba(0, 0, 0, 1) 100%)", "gradient", {
              type: "linear",
              angle: "0",
              colors: [{
                  r: "255",
                  g: "255",
                  b: "255",
                  a: "1",
                  position: "0",
                  align: "bottom"
              }, {
                  r: "0",
                  g: "0",
                  b: "0",
                  a: "1",
                  position: "100",
                  align: "bottom"
              }]
          }]
      }
  },
  transparentRgba: function(e, r) {
      return !(!r && "rgba" !== RevColor.process(e)[1]) && "0" === RevColor.rgbValues(e, 4)[3]
  },
  rgbValues: function(e, r) {
      3 === (e = e.substring(e.indexOf("(") + 1, e.lastIndexOf(")")).split(",")).length && 4 === r && (e[3] = "1");
      for (var a = 0; a < r; a++) e[a] = e[a].trim();
      return e
  },
  rgbaString: function(e, r, a, t) {
      return "rgba(" + e + ", " + r + ", " + a + ", " + t + ")"
  },
  rgbToHex: function(e) {
      var r = RevColor.rgbValues(e, 3);
      return RevColor.getRgbToHex(r[0], r[1], r[2])
  },
  rgbaToHex: function(e) {
      var r = RevColor.rgbValues(e, 4);
      return [RevColor.getRgbToHex(r[0], r[1], r[2]), r[3]]
  },
  getOpacity: function(e) {
      return parseInt(100 * RevColor.rgbValues(e, 4)[3], 10) + "%"
  },
  getRgbToHex: function(e, r, a) {
      return "#" + ("0" + parseInt(e).toString(16)).slice(-2) + ("0" + parseInt(r).toString(16)).slice(-2) + ("0" + parseInt(a).toString(16)).slice(-2)
  },
  joinToRgba: function(e) {
      return e = e.split("||"), RevColor.convert(e[0], e[1])
  },
  processRgba: function(e, r) {
      e = e.replace("#", "");
      var a = void 0 !== r,
          t = (a ? "rgba" : "rgb") + "(" + parseInt(e.substring(0, 2), 16) + ", " + parseInt(e.substring(2, 4), 16) + ", " + parseInt(e.substring(4, 6), 16);
      return t += a ? ", " + (.01 * parseInt(r, 10)).toFixed(2).replace(/\.?0*$/, "") + ")" : ")"
  },
  processGradient: function(e) {
      for (var r, a = e.type, t = a + "-gradient(", i = "linear" === a ? e.angle + "deg, " : "ellipse at center, ", o = e.colors, c = o.length, n = "", s = 0; s < c; s++) s > 0 && (n += ", "), n += "rgba(" + (r = o[s]).r + ", " + r.g + ", " + r.b + ", " + r.a + ") " + r.position + "%";
      return t + i + n + ")"
  },
  sanitizeHex: function(e) {
      if (3 === (e = e.replace("#", "").trim()).length) {
          var r = e.charAt(0),
              a = e.charAt(1),
              t = e.charAt(2);
          e = r + r + a + a + t + t
      }
      return "#" + e
  },
  sanitizeGradient: function(e) {
      for (var r, a = e.colors, t = a.length, i = [], o = 0; o < t; o++) {
          var c = a[o];
          delete c.align, r ? JSON.stringify(c) !== JSON.stringify(r) && (i[i.length] = c) : i[i.length] = c, r = c
      }
      return e.colors = i, e
  }
},
function(e) {
  function r(e, r) {
      var a = (e = e.split("("))[0];
      e.shift();
      var t = e.join("(").split(",");
      return t.shift(), r = void 0 !== r ? r + "deg," : "ellipse at center,", a + "(" + r + t.join(",")
  }

  function a(e) {
      return r(e.replace("radial-", "linear-"), "90")
  }

  function t() {
      this.innerHTML = rr[c(this, "data-text")]
  }

  function i() {
      this.setAttribute("placeholder", rr[c(this, "data-placeholder")])
  }

  function o() {
      this.setAttribute("data-message", rr[c(this, "data-alert")])
  }

  function c(e, r) {
      return e.getAttribute(r) || ""
  }

  function n(r) {
      r || (r = {}), "string" == typeof r && (r = JSON.parse(r.replace(/\&/g, '"'))), rr = e.extend({}, Or, r), Te = rr.color, ce.find("*[data-placeholder]").each(i), ce.find("*[data-alert]").each(o), ce.find("*[data-text]").each(t)
  }

  function s(r, a, t, i) {
      var o, c, n;
      if (e.isPlainObject(r)) {
          var s, l;
          for (var p in r) r.hasOwnProperty(p) && ("string" == typeof(r = r[p]) ? ("gradient" === (r = RevColor.process(r))[1] && (s = (c = r[2]).angle, l = c.type), r = r[0]) : (s = r.angle, l = r.type), o = isNaN(p) ? p.replace(/_/g, " ").replace(/\b\w/g, function(e) {
              return e.toUpperCase()
          }) : "radial" !== l ? s + "&deg;" : "radial")
      } else o = r;
      if ("blank" !== r) {
          e.isPlainObject(r) && (c = r, "", r = i || RevColor.processGradient(r));
          var v = '<span class="rev-cpicker-color tptip' + t + '" data-title="' + o + '" data-color="' + r + '"><span class="rev-cpicker-preset-tile"></span><span class="rev-cpicker-preset-bg" style="background: ' + r + '"></span>';
          return a || (v += '<span class="rev-cpicker-delete"><span class="rev-cpicker-delete-icon"></span></span>'), v += "</span>", n = e(v), c && n.data("gradient", c), n[0]
      }
      return n = document.createElement("span"), n.className = "rev-cpicker-color blank", n
  }

  function l() {
      var r = c(this, "data-color").toLowerCase(),
          a = !kr && r === pr.toLowerCase();
      if (r === ir || a) {
          var t = e(this);
          return t.closest(".rev-cpicker-presets-group").find(".rev-cpicker-color.selected").removeClass("selected"), Cr = t, kr && !De && d(Cr.data("gradient"), !0), Cr.addClass("selected"), !1
      }
  }

  function p(e, r) {
      for (var a = document.createDocumentFragment(), t = -1 !== e.search("core"), i = t ? "" : " rev-picker-color-custom", o = r.length, c = -1 !== e.search("colors") ? Ar : Mr, n = Math.max(Math.ceil(o / Hr), c), l = 0; l < n; l++)
          for (; r.length < (l + 1) * Hr;) r[r.length] = "blank";
      for (o = r.length, l = 0; l < o; l++) a.appendChild(s(r[l], t, i));
      return ["rev-cpicker-" + e, a]
  }

  function v(e, r, a) {
      if (dr) {
          if (!e) {
              var t = r || fe.val(),
                  i = void 0 !== a ? a : sr.val();
              r = "transparent" === t ? "transparent" : "100%" === i ? RevColor.sanitizeHex(t) : RevColor.processRgba(t, i)
          }
          var o = "transparent" === r,
              c = o ? "" : r;
          e ? le.data("state", r) : ue.data("state", r), o ? dr.css("background", c) : dr[0].style.background = c, te && te(or, r), X.trigger("revcolorpickerupdate", [or, r])
      }
  }

  function d(e, r) {
      var a = RevColor.process(e),
          t = a[1],
          i = a[0];
      if (K && se.removeClass("checked"), "gradient" !== t) {
          switch (t) {
              case "hex":
                  e = RevColor.sanitizeHex(i), sr.val("100%"), G(100);
                  break;
              case "rgba":
                  var o = RevColor.rgbaToHex(i),
                      c = parseInt(100 * o[1], 10);
                  e = o[0], sr.val(c + "%"), G(c);
                  break;
              case "rgb":
                  e = RevColor.rgbToHex(i), sr.val("100%"), G(100);
                  break;
              default:
                  qe.click(), ue.click()
          }
          Ve.val(e).change(), r || ue.click()
      } else K ? (I(a[2]), N(), r || (We = !0, le.click())) : (Ve.val(RevColor.defaultValue).change(), ue.click());
      return [i, t]
  }

  function g(e, r) {
      var a, t = ee.slice(),
          i = t.length;
      for (t.sort(T); i--;)
          if ((a = t[i]).align === e && a.x < r) return a;
      i = t.length;
      for (var o = 0; o < i; o++)
          if ((a = t[o]).align === e && a.x > r) return a
  }

  function b(r, a) {
      var t = g(r, a).color,
          i = u(t, r, !0),
          o = h(r, a, f(t, !0), i);
      xe && xe.removeClass("active"), xe = e(o).addClass("active").appendTo(Ze).draggable(Er), He = xe.children(".rev-cpicker-point-square")[0], Se = xe.children(".rev-cpicker-point-triangle")[0], Ue = Ze.children();
      var c = k(a);
      N(o), "bottom" === r && he.val(c[1]).change()
  }

  function k(e) {
      void 0 === e && (e = ee[Je].x);
      var r = xe.attr("data-color"),
          a = xe.hasClass("rev-cpicker-point-bottom");
      if (a) hr.hasClass("active") && (Qe.attr("disabled", "disabled"), Ye.attr("disabled", "disabled"), hr.removeClass("active")), r = RevColor.rgbaToHex(r)[0], ke.css("background", r), me.removeAttr("disabled").val(e + "%"), ce.find(".rev-cpicker-point-bottom").length > 2 && er.addClass("active"), ce.addClass("open");
      else {
          er.hasClass("active") && (ke.css("background", ""), me.attr("disabled", "disabled"), er.removeClass("active"));
          var t = RevColor.getOpacity(r);
          Qe.attr("data-opacity", t).val(t).removeAttr("disabled"), Ye.val(e + "%").removeAttr("disabled"), ce.find(".rev-cpicker-point-top").length > 2 && hr.addClass("active"), ce.removeClass("open")
      }
      return [a, r]
  }

  function u(e, r, a) {
      return "bottom" === r ? "rgb(" + e.r + "," + e.g + "," + e.b + ")" : "rgba(0, 0, 0, " + (a ? "1" : e.a) + ")"
  }

  function f(e, r) {
      var a = r ? "1" : e.a;
      return "rgba(" + e.r + "," + e.g + "," + e.b + "," + a + ")"
  }

  function m(e) {
      for (var r, a, t = document.createDocumentFragment(), i = e.length, o = 0; o < i; o++) r = (a = e[o]).align, t.appendChild(h(r, a.position, f(a), u(a, r)));
      Ue && Ue.draggable("destroy"), Ze.empty().append(t), Ue = Ze.children().draggable(Er)
  }

  function h(r, a, t, i) {
      var o = document.createElement("span");
      return o.className = "rev-cpicker-point rev-cpicker-point-" + r, "string" == typeof t ? o.setAttribute("data-color", t) : e(o).data("gradient", t), o.setAttribute("data-location", a), o.style.left = a + "%", o.innerHTML = "bottom" === r ? '<span class="rev-cpicker-point-triangle" style="border-bottom-color: ' + i + '"></span><span class="rev-cpicker-point-square" style="background: ' + i + '"></span>' : '<span class="rev-cpicker-point-square" style="background: ' + i + '"></span><span class="rev-cpicker-point-triangle" style="border-top-color: ' + i + '"></span>', o
  }

  function C(e) {
      return e && "radial" !== e || (e = "0"), Ae.innerHTML = e + "&deg;", Ae.value
  }

  function x() {
      xe && (xe.removeClass("active"), xe = !1), me.attr("disabled", "disabled"), Qe.attr("disabled", "disabled"), Ye.attr("disabled", "disabled"), hr.removeClass("active"), er.removeClass("active"), ke.css("background", ""), ce.removeClass("open")
  }

  function y(e, r) {
      ce.removeClass("active is-basic").hide(), Z.removeClass("rev-colorpicker-open"), mr.css({
          left: "",
          top: ""
      }), ye && (ye.remove(), ye = !1), Cr ? (Cr.hasClass("selected") ? (r && or.data("hex", Cr.attr("data-color").toLowerCase()), Cr.removeClass("selected")) : or.removeData("hex"), Cr = !1) : or.removeData("hex"), r || (Re && Re(), lr && "transparent" !== lr ? dr[0].style.background = lr : dr.css("background", ""), X.trigger("revcolorpickerupdate", [or, lr])), dr = !1, or = !1
  }

  function w() {
      var r = e(this).children(".rev-cpicker-color").not(".blank").length;
      return r > Hr ? e("#" + this.id + "-btn").addClass("multiplerows") : e("#" + this.id + "-btn").removeClass("multiplerows"), r
  }

  function R() {
      var r, a = e(this),
          t = -1 !== this.id.search("colors") ? Ar : Mr,
          i = a.children(".rev-cpicker-color"),
          o = i.length,
          c = Math.ceil(o / Hr),
          n = t * Hr;
      o += 1;
      for (var s = 0; s < c; s++) {
          var l = s * Hr,
              p = i.slice(l, parseInt(l + Hr, 10) - 1);
          Ce = !0, p.each(_), Ce && (o -= Hr) >= n && (p.remove(), r = !0)
      }
      return r
  }

  function _() {
      if (-1 === this.className.search("blank")) return Ce = !1, !1
  }

  function I(r) {
      var a = r.angle;
      "radial" === r.type && (a = "radial"), $e.removeClass("selected"), e('.rev-cpicker-orientation[data-direction="' + a + '"]').addClass("selected"), Q.val(C(a)), O(a), m(r.colors)
  }

  function N(e, r, t) {
      Oe = r, V(), Oe = !1;
      for (var i, o, c, n = [], s = ee.length, l = 0; l < s; l++) o = (c = ee[l]).color, n[l] = o, (i = c.el).setAttribute("data-color", RevColor.rgbaString(o.r, o.g, o.b, o.a)), i.setAttribute("data-opacity", 100 * o.a), e && e === i && (Je = l);
      re.hasClass("selected") ? (Ir.type = "radial", Ir.angle = "0") : (Ir.type = "linear", Ir.angle = parseInt(Q.val(), 10).toString()), Ir.colors = n, Cr && Cr.removeClass("selected");
      var p = RevColor.processGradient(Ir);
      if (v(!0, p), t) return [Ir, p];
      Ee.style.background = a(p), Ge.style.background = p
  }

  function F(e, r) {
      if (0 === e) return !1;
      for (var a; e--;)
          if ((a = ee[e]).align !== r) return a;
      return !1
  }

  function j(e, r, a) {
      if (e === a) return !1;
      for (var t; e++ < a;)
          if ((t = ee[e]).align !== r) return t;
      return !1
  }

  function M(e, r, a, t, i) {
      return Math.max(Math.min(Math.round(Math.abs((e - r) / (a - r) * (i - t) + t)), 255), 0)
  }

  function A(e, r, a, t, i) {
      return Math.max(Math.min(Math.abs(parseFloat(((e - r) / (a - r) * (i - t)).toFixed(2)) + parseFloat(t)), 1), 0)
  }

  function H(e, r, a) {
      var t, i = r.alpha,
          o = a.alpha;
      t = i !== o ? A(e.x, r.x, a.x, i, o).toFixed(2) : i, e.alpha = t, e.color.a = t
  }

  function S(e, r, a) {
      var t = e.color,
          i = r.color,
          o = a.color;
      if (r !== a) {
          var c = e.x,
              n = r.x,
              s = a.x;
          t.r = M(c, n, s, i.r, o.r), t.g = M(c, n, s, i.g, o.g), t.b = M(c, n, s, i.b, o.b)
      } else t.r = i.r, t.g = i.g, t.b = i.b
  }

  function V() {
      ee = [], Fe = [], je = [], Ue.each(E), ee.sort(T);
      for (var e, r, a, t, i = ee.length, o = i - 1, c = 0; c < i; c++) !1 === (r = F(c, a = (e = ee[c]).align)) && (r = j(c, a, o)), !1 === (t = j(c, a, o)) && (t = F(c, a)), "bottom" === a ? H(e, r, t) : S(e, r, t);
      ee.sort(T)
  }

  function T(e, r) {
      return e.x < r.x ? -1 : e.x > r.x ? 1 : 0
  }

  function E(e) {
      var r = RevColor.rgbValues(c(this, "data-color"), 4),
          a = -1 !== this.className.search("bottom") ? "bottom" : "top",
          t = r[3].replace(/\.?0*$/, "") || 0,
          i = parseInt(this.style.left, 10);
      Oe && (i < 50 ? i += 2 * (50 - i) : i -= 2 * (i - 50), this.style.left = i + "%", this.setAttribute("data-location", i)), ee[e] = {
          el: this,
          x: i,
          alpha: t,
          align: a,
          color: {
              r: parseInt(r[0], 10),
              g: parseInt(r[1], 10),
              b: parseInt(r[2], 10),
              a: t,
              position: i,
              align: a
          }
      }, xe && xe[0] !== this && ("bottom" === a ? je[je.length] = i : Fe[Fe.length] = i)
  }

  function O(e) {
      e = void 0 !== e ? e : parseInt(Q.val(), 10), Be[0].style.transform = "rotate(" + e + "deg)"
  }

  function P(r, a, t) {
      var i, o, c = void 0 !== t,
          n = c ? t : parseInt(Q.val(), 10);
      if (r && "keyup" === r.type) i = !isNaN(n) && n >= -360 && n <= 360, o = n;
      else {
          var s = parseInt(Q.data("orig-value"), 10);
          n || (n = "0"), (isNaN(n) || n < -360 || n > 360) && (n = s), n !== s && (o = n, i = !0, Q.val(C(n)), c || (n = a || n, $e.removeClass("selected"), e('.rev-cpicker-orientation[data-direction="' + n + '"]').addClass("selected")))
      }(i || a) && (o && O(o), N())
  }

  function z() {
      var r = e(this); - 1 !== this.className.search("down") ? (r.parent().addClass("active"), r.closest(".rev-cpicker-presets").addClass("active"), e(this.id.replace("-btn", "")).addClass("active"), ur = ce.hasClass("gradient-view")) : (r.parent().removeClass("active"), r.closest(".rev-cpicker-presets").removeClass("active"), e(this.id.replace("-btn", "")).removeClass("active"), ur = !1)
  }

  function D(e, r) {
      var a = parseInt(100 * (Math.round(r.position.left) / (Nr - 2)).toFixed(2), 10);
      "bottom" === Ne ? me.val(a + "%").trigger("keyup") : Ye.val(a + "%").trigger("keyup")
  }

  function L() {
      var r = e(this);
      Ne = r.hasClass("rev-cpicker-point-bottom") ? "bottom" : "top", r.click()
  }

  function B() {
      "bottom" === Ne ? me.trigger("focusout") : Ye.trigger("focusout")
  }

  function G(e) {
      yr = !0, xr.slider("value", Math.round(.01 * e * Tr)), yr = !1
  }

  function q(e) {
      var r = Le.offset(),
          a = e.pageX - r.left,
          t = e.pageY - r.top;
      if (!isNaN(a) && !isNaN(t)) {
          var i = Math.atan2(t - jr, a - jr) * (180 / Math.PI) + 90;
          i < 0 && (i += 360), i = Math.max(0, Math.min(360, Math.round(i))), i = 5 * Math.round(i / 5), br = !0, P(!1, !1, i), br = !1
      }
  }

  function $(e) {
      e.stopImmediatePropagation()
  }

  function J() {
      ne || e.tpColorPicker(), wr = document.getElementById("rev-cpicker-current-edit"), Ge = document.getElementById("rev-cpicker-gradient-output"), Ee = document.getElementById("rev-cpicker-gradient-input"), ze = document.getElementById("rev-cpicker-edit-title"), Ae = document.createElement("textarea"), hr = e("#rev-cpicker-opacity-delete"), Ze = e("#rev-cpciker-point-container"), Ye = e("#rev-cpicker-opacity-location"), cr = e(".rev-cpicker-presets-group"), sr = e("#rev-cpicker-color-opacity"), re = e("#rev-cpicker-orientation-radial"), er = e("#rev-cpicker-color-delete"), Qe = e("#rev-cpicker-grad-opacity"), me = e("#rev-cpicker-color-location"), we = e("#rev-cpicker-gradients-core"), $e = e(".rev-cpicker-orientation"), he = e("#rev-cpicker-iris-gradient"), Be = e("#rev-cpicker-wheel-point"), Ke = e("#rev-cpicker-gradients"), Ve = e("#rev-cpicker-iris-color"), le = e("#rev-cpicker-gradient-btn"), pe = e("#rev-cpicker-gradient-hex"), qe = e("#rev-cpciker-clear-hex"), se = e("#rev-cpicker-meta-reverse"), Me = e("#rev-cpicker-hit-bottom"), xr = e("#rev-cpicker-scroll"), nr = e("#rev-cpicker-colors"), fe = e("#rev-cpicker-color-hex"), ue = e("#rev-cpicker-color-btn"), ke = e("#rev-cpicker-color-box"), Q = e("#rev-cpicker-meta-angle"), Le = e("#rev-cpicker-wheel"), ae = e("#rev-cpicker-hit-top"), mr = e("#rev-cpicker"), X = e(document), Er.drag = D, Er.stop = B, Er.start = L, ue.data("state", nr.find(".rev-cpicker-color").eq(0).attr("data-color") || "#ffffff"), le.data("state", Ke.find(".rev-cpicker-color").eq(0).attr("data-color") || "linear-gradient(0deg, rgba(255, 255, 255, 1) 0%, rgba(0, 0, 0, 1) 100%)"), mr.draggable({
          containment: "window",
          handle: ".rev-cpicker-draggable",
          stop: function() {
              mr.css("height", "auto")
          }
      }), cr.perfectScrollbar({
          wheelPropagation: !1,
          suppressScrollX: !0
      }), Le.on("mousedown.revcpicker", function(e) {
          $e.removeClass("selected"), Pe = !0, q(e)
      }).on("mousemove.revcpicker", function(e) {
          Pe && q(e)
      }).on("mouseleave.revcpicker mouseup.revcpicker", function() {
          Pe = !1
      }), e(".rev-cpicker-main-btn").on("click.revcpicker", function() {
          var r;
          if (De = -1 === this.id.search("gradient"), dr && (r = e(this).data("state")), De ? (dr && (ir = fe.val()), ce.removeClass("gradient-view").addClass("color-view")) : (dr && (ir = r), ce.removeClass("color-view").addClass("gradient-view"), We || we.children(".rev-cpicker-color").not(".blank").eq(0).click()), cr.perfectScrollbar("update"), r) {
              var a = "transparent" === r,
                  t = a ? "" : r;
              a ? dr.css("background", t) : dr[0].style.background = t, kr = !0, e(".rev-cpicker-color").not(".blank").each(l), kr = !1, X.trigger("revcolorpickerupdate", [or, r])
          }
      }), e("#rev-cpicker-check").on("click.revcipicker", function() {
          var r, a, t;
          if (ce.hasClass("color-view")) {
              var i = fe.val(),
                  o = sr.val();
              or.removeData("gradient"), a = "transparent" === i ? "transparent" : "100%" === o ? RevColor.sanitizeHex(i) : RevColor.processRgba(i, o), r = [or, a, !1]
          } else {
              x();
              var c = N(!1, !1, !0),
                  n = e.extend({}, c[0]),
                  s = c[1];
              or.data("gradient", s), a = JSON.stringify(n).replace(/\"/g, "&"), r = [or, s, n]
          }(t = r[1] !== lr) && (or.attr("data-color", r[1]).val(a).change(), X.trigger("revcolorpicker", r), _r && _r(r[0], r[1], r[2])), y(!1, t)
      }), ce.on("click.revcpicker", function(r) {
          if (ce.hasClass("open")) {
              var a = r.target,
                  t = e(a),
                  i = a.id,
                  o = -1 !== a.className.search("rev-cpicker-point") || "rev-cpicker-section-right" === i || -1 !== i.search("hit") || t.closest("#rev-cpicker-section-right, #rev-cpicker-point-wrap").length;
              o && ("text" === t.attr("type") ? o = !t.attr("disabled") : "rev-cpicker-check-gradient" === i && (o = !1)), o || x()
          } else Xe && !1 === /wheel|angle|reverse/.test(r.target.id) && (-1 === r.target.id.search("radial") && e('.rev-cpicker-orientation[data-direction="' + parseInt(Q.val()) + '"]').addClass("selected"), Le.removeClass("active"), Xe = !1)
      }), e(".rev-cpicker-close").on("click.revcpicker", y), Ve.wpColorPicker({
          palettes: !1,
          width: 267,
          border: !1,
          hide: !1,
          change: function(e, r) {
              var a = r.color.toString();
              if (this.value = a, fe.val(a), !gr) {
                  var t = sr.val();
                  0 === parseInt(t, 10) && (a = "transparent"), v(!1, a, t), Cr && (Cr.removeClass("selected"), Cr = !1)
              }
          }
      }), he.wpColorPicker({
          palettes: !1,
          height: 250,
          border: !1,
          hide: !1,
          change: function(e, r) {
              var a = r.color.toString();
              this.value = a, pe.val(a), ke.css("background", a), He.style.backgroundColor = a, Se.style.borderBottomColor = a;
              var t = RevColor.processRgba(a, 100),
                  i = RevColor.rgbValues(t, 4),
                  o = Ir.colors[Je];
              o.r = i[0], o.g = i[1], o.b = i[2], o.a = i[3], xe.attr("data-color", t), N()
          }
      }), xr.slider({
          orientation: "vertical",
          max: Tr,
          value: Tr,
          start: function() {
              fr = "transparent" === fe.val()
          },
          slide: function(e, r) {
              if (!yr) {
                  var a, t = parseInt(100 * (r.value / Tr).toFixed(2), 10);
                  fr && (a = t ? "#ffffff" : "transparent", fe.val(a)), 0 === t && (a = "transparent"), v(!1, a, t || "transparent"), sr.val(t + "%")
              }
          }
      }), e(".rev-cpicker-point-location").on("keyup.revcpicker focusout.revcpicker", function(e) {
          if (xe) {
              var r, a = xe.hasClass("rev-cpicker-point-bottom") ? "bottom" : "top",
                  t = "bottom" === a ? je : Fe,
                  i = "bottom" === a ? me : Ye,
                  o = i.val().replace("%", "") || "0",
                  c = e.type;
              for (isNaN(o) && (o = "keyup" === c ? "0" : xe.attr("data-location")), r = (o = Math.max(0, Math.min(100, parseInt(o, 10)))) < 50 ? 1 : -1; - 1 !== t.indexOf(o);) o += r;
              "focusout" === c && (i.val(o + "%"), xe.attr("data-location", o)), xe.css("left", o + "%"), N()
          }
      }).on("focusin.revcpicker", $), e("body").on("click.revcpicker", ".rev-cpicker-point", function() {
          Ze.find(".rev-cpicker-point.active").removeClass("active"), xe = e(this).addClass("active"), He = xe.children(".rev-cpicker-point-square")[0], Se = xe.children(".rev-cpicker-point-triangle")[0], N(this), Cr = !1;
          var r = k();
          r[0] && he.val(r[1]).change()
      }).on("mousedown.revcpicker", ".rev-cpicker-point", function(r) {
          xe = e(this).data("mousestart", r.pageY)
      }).on("mousemove.revcpicker", function(e) {
          if (xe && xe.data("mousestart")) {
              var r = xe.data("mousestart"),
                  a = e.pageY;
              xe.hasClass("rev-cpicker-point-bottom") ? a > r && a - r > Vr && er.hasClass("active") ? xe.addClass("warning") : xe.removeClass("warning") : r > a && r - a > Vr && hr.hasClass("active") ? xe.addClass("warning") : xe.removeClass("warning")
          }
      }).on("mouseup.revcpicker", function(e) {
          if (xe && xe.data("mousestart")) {
              var r = xe.data("mousestart"),
                  a = e.pageY;
              xe.removeData("mousestart"), xe.hasClass("rev-cpicker-point-bottom") ? a > r && a - r > Sr && er.hasClass("active") ? er.click() : xe.removeClass("warning") : r > a && r - a > Sr && hr.hasClass("active") ? hr.click() : xe.removeClass("warning")
          }
      }).on("change.revcpicker", ".rev-cpicker-component", function() {
          var r = e(this),
              a = r.data("gradient") || r.val() || "transparent";
          ("transparent" === a || RevColor.transparentRgba(a)) && (a = ""), r.data("tpcp").css("background", a)
      }).on("keypress.revcpicker", function(e) {
          if (ce.hasClass("active")) {
              var r = e.which;
              27 == r ? y() : 13 == r && de && de.blur()
          }
      }).on("click.revcpicker", ".rev-cpicker-color:not(.blank)", function() {
          if (Cr) {
              if (Cr[0] === this && Cr.hasClass("selected")) return;
              Cr.removeClass("selected")
          }
          var r = (Cr = e(this)).parent()[0].id,
              a = -1 !== r.search("core") ? "core" : "custom",
              t = -1 !== r.search("colors") ? "colors" : "gradients",
              i = e("#rev-cpicker-" + t + "-" + a + "-btn");
          if (i.hasClass("active") && i.find(".rev-cpicker-arrow-up").click(), ce.hasClass("color-view")) {
              var o = Cr.attr("data-color");
              gr = !0, Ve.val(o).change(), "transparent" === fe.val() && fe.val(o.toLowerCase()), gr = !1;
              var c = sr.val();
              0 === parseInt(c, 10) && (o = "transparent"), v(!1, o, c)
          } else ae.removeClass("full"), Me.removeClass("full"), d(Cr.data("gradient"), !0), se.removeClass("checked"), we.find(".rev-cpicker-color.selected").removeClass("selected");
          Cr.addClass("selected")
      }).on("mouseover.revcpicker", ".rev-cpicker-color:not(.blank)", function() {
          ur && (Ge.style.background = c(this, "data-color"))
      }).on("mouseout.revcpicker", ".rev-cpicker-color:not(.blank)", function() {
          ur && N()
      }).on("click.revcpicker", ".rev-cpicker-delete", function() {
          if (oe) {
              if (window.confirm(document.getElementById("rev-cpicker-remove-delete").innerHTML)) {
                  ce.addClass("onajax onajaxdelete");
                  var r = e(this),
                      a = r.parent(),
                      t = a.attr("data-title") || "";
                  if (!t) return void console.log("Preset does not have a name/title");
                  var i = r.closest(".rev-cpicker-presets-group")[0].id,
                      o = -1 !== i.search("colors") ? "colors" : "gradients";
                  X.off("revcpicker_onajax_delete.revcpicker").on("revcpicker_onajax_delete.revcpicker", function(t, o) {
                      o && console.log(o);
                      var c = r.closest(".rev-cpicker-presets-group"),
                          n = c.find(".ps-scrollbar-x-rail"),
                          s = e("#" + i + "-btn");
                      a.remove(), R.call(c[0]) ? c.perfectScrollbar("update") : e('<span class="rev-cpicker-color blank"></span>').insertBefore(n), w.call(c[0]) < Hr + 1 && (e('<span class="rev-cpicker-color blank"></span>').insertBefore(n), s.hasClass("active") && s.children(".rev-cpicker-arrow-up").click()), ce.removeClass("onajax onajaxdelete")
                  }), t = e.trim(t.replace(/\W+/g, "_")).replace(/^\_|\_$/g, "").toLowerCase(), oe("delete", t, o, "revcpicker_onajax_delete", or)
              }
              return !1
          }
          console.log("Ajax callback not defined")
      }), e(".rev-cpicker-save-preset-btn").on("click.revcpicker", function() {
          if (oe) {
              var r, a, t = e(this),
                  i = t.closest(".rev-cpicker-presets-save-as").find(".rev-cpicker-preset-save").val();
              if (i && isNaN(i)) {
                  if (r = ce.hasClass("color-view") ? "colors" : "gradients", i = e.trim(i.replace(/\W+/g, "_")).replace(/^\_|\_$/g, "").toLowerCase(), e("#rev-cpicker-" + r + "-custom").find(".rev-cpicker-color").not(".blank").each(function() {
                          if (e.trim(c(this, "data-title").replace(/\W+/g, "_")).replace(/^\_|\_$/g, "").toLowerCase() === i) return alert(t.attr("data-message")), a = !0, !1
                      }), !a) {
                      ce.addClass("onajax onajaxsave");
                      var o, n, l = {};
                      if ("colors" === r) {
                          var p = fe.val(),
                              v = sr.val();
                          o = "transparent" === p ? "transparent" : "100%" === v ? RevColor.sanitizeHex(p) : RevColor.processRgba(p, v)
                      } else n = Ge.style.background, o = e.extend({}, N(!1, !1, !0)[0]);
                      l[i] = o, X.off("revcpicker_onajax_save.revcpicker").on("revcpicker_onajax_save.revcpicker", function(a, i) {
                          if (i) return ce.removeClass("onajax onajaxsave"), void alert(t.attr("data-message"));
                          var o = e(s(l, !1, " rev-picker-color-custom", n)),
                              c = e("#rev-cpicker-" + r + "-custom"),
                              p = c.find(".rev-cpicker-color.blank"),
                              v = e("#" + c[0].id + "-btn");
                          p.length ? o.insertBefore(p.eq(0)) : o.insertBefore(c.find(".ps-scrollbar-x-rail")), e("#rev-cpicker-" + r + "-custom-btn").click(), w.call(c[0]) > 6 && (p.length && p.last().remove(), v.addClass("active").children(".rev-cpicker-arrow-down").click(), c.perfectScrollbar("update")), o.click(), ce.removeClass("onajax onajaxsave")
                      }), oe("save", l, r, "revcpicker_onajax_save", or)
                  }
              } else alert(t.attr("data-message"))
          } else console.log("Ajax callback not defined")
      }), e(".rev-cpicker-preset-title").on("click.revcpicker", function() {
          var r = e(this),
              a = r.parent(),
              t = r.hasClass("active") ? "down" : "up";
          z.call(r.find(".rev-cpicker-arrow-" + t)[0]), a.find(".rev-cpicker-preset-title").removeClass("selected"), r.addClass("selected"), a.find(".rev-cpicker-presets-group").hide(), document.getElementById(this.id.replace("-btn", "")).style.display = "block", cr.perfectScrollbar("update")
      }), qe.on("click.revcpicker", function() {
          sr.val("0%"), G(0), Ve.val(RevColor.defaultValue).change(), fe.val("transparent"), v(!1, "transparent")
      }), ce.find('input[type="text"]').on("focusin.revcpicker", function() {
          de = this
      }).on("focusout.revcpicker", function() {
          de = !1
      }), e(".rev-cpicker-input").on("focusin.revcpicker", function() {
          var r = e(this);
          r.data("orig-value", r.val())
      }), e(".rev-cpicker-hex").on("focusout.revcpicker", function() {
          var r, a, t;
          if ("rev-cpicker-color-hex" === this.id) {
              if (t = fe.val())
                  if (t = RevColor.sanitizeHex(t), RevColor.isColor.test(t)) fe.val(t);
                  else {
                      if (r = e(this), !(a = r.data("orig-value"))) return void qe.click();
                      t = a, fe.val(t)
                  }
              else t = "transparent";
              Ve.val(t).change()
          } else t = pe.val() || RevColor.defaultValue, t = RevColor.sanitizeHex(t), RevColor.isColor.test(t) || (t = (a = (r = e(this)).data("orig-value")) || RevColor.defaultValue), pe.val(t), he.val(t).change()
      }).on("focusin.revcpicker", $), e("#rev-cpciker-clear-gradient").on("click.revcpicker", function() {
          he.val(RevColor.defaultValue).change()
      }), Q.on("keyup.revcpicker focusout.revcpicker", P).on("focusin.revcpicker", function() {
          Xe = !0, Le.addClass("active")
      }).on("focusin.revcpicker", $), $e.on("click.revcpicker", function() {
          var r = e(this),
              a = r.attr("data-direction");
          $e.removeClass("selected"), r.addClass("selected"), "radial" !== a ? Q.removeAttr("disabled").val(C(a)) : Q.attr("disabled", "disabled"), P(!1, a)
      }), e(".rev-cpicker-point-delete").on("click.revcpicker", function() {
          if (-1 !== this.className.search("active")) {
              var e = xe.hasClass("rev-cpicker-point-bottom") ? "bottom" : "top",
                  r = ce.find(".rev-cpicker-point-" + e).length;
              r > 2 && (xe.draggable("destroy").remove(), Ue = Ze.children(), ce.click(), N()), r <= Fr && ("bottom" === e ? Me.removeClass("full") : ae.removeClass("full"))
          }
      }), e(".rev-cpicker-preset-save").on("focusin.revcpicker", $), e(".rev-cpicker-opacity-input").on("keyup.revcpicker focusout.revcpicker", function(r) {
          var a, t = -1 === this.id.search("grad"),
              i = t ? sr : Qe,
              o = i.val().replace("%", ""),
              c = r.type;
          if (isNaN(o) && (o = "keyup" === c ? "0" : e(this).data("orig-value")), o = Math.max(0, Math.min(100, o)), "focusout" === c && (i.val(o + "%"), t || xe.attr("data-opacity", o)), t) v(!1, 0 === parseInt(o, 10) && "transparent", o), G(o);
          else {
              var n = RevColor.rgbValues(xe.attr("data-color"), 3),
                  s = Ir.colors[Je];
              o = (.01 * parseInt(o, 10)).toFixed(2).replace(/\.?0*$/, ""), s.r = n[0], s.g = n[1], s.b = n[2], s.a = o, a = RevColor.rgbaString(s.r, s.g, s.b, o), xe.attr("data-color", a), N(), a = "rgba(0, 0, 0, " + o + ")", He.style.backgroundColor = a, Se.style.borderTopColor = a
          }
      }).on("focusin.revcpicker", $), e(".rev-cpicker-builder-hit").on("click.revcpicker", function(e) {
          ee || V();
          for (var r = parseInt(100 * ((e.pageX - ae.offset().left) / Nr).toFixed(2), 10), a = -1 !== this.id.search("bottom") ? "bottom" : "top", t = "bottom" === a ? je : Fe, i = r < 50 ? 1 : -1; - 1 !== t.indexOf(r);) r += i;
          "bottom" === a ? ce.find(".rev-cpicker-point-bottom").length < Fr ? (b(a, r), Cr = !1) : Me.addClass("full") : ce.find(".rev-cpicker-point-top").length < Fr ? (b(a, r), Cr = !1) : ae.addClass("full")
      }), se.on("click.revcpicker", function() {
          !se.hasClass("checked") ? se.addClass("checked") : se.removeClass("checked"), N(!1, !0)
      }), e(".rev-cpicker-arrow").on("click.revcpicker", z), U = !0
  }

  function Y(r) {
      var a, t, i, o, c, n = e.extend({}, r),
          s = n.core || {},
          l = n.custom;
      !ar || l ? (o = 4, l = (ar = l) || {
          colors: [],
          gradients: []
      }) : o = 2, s.colors || (s.colors = Pr), s.gradients || (s.gradients = zr);
      for (var v = 0; v < o; v++) {
          switch (v) {
              case 0:
                  a = "colors-core", i = s.colors;
                  break;
              case 1:
                  a = "gradients-core", i = s.gradients;
                  break;
              case 2:
                  a = "colors-custom", i = l.colors;
                  break;
              case 3:
                  a = "gradients-custom", i = l.gradients
          }
          t = p(a, i.slice() || []), (c = e("#" + t[0])).find(".rev-cpicker-color").remove(), c.prepend(t[1])
      }
  }

  function W(e) {
      return !!/(?=.*false)(?=.*rgba)/.test(e) && e.replace("false", "1")
  }
  var X, Q, U, Z, K, ee, re, ae, te, ie, oe, ce, ne, se, le, pe, ve, de, ge, be, ke, ue, fe, me, he, Ce, xe, ye, we, Re, _e, Ie, Ne, Fe, je, Me, Ae, He, Se, Ve, Te, Ee, Oe, Pe, ze, De, Le, Be, Ge, qe, $e, Je, Ye, We, Xe, Qe, Ue, Ze, Ke, er, rr, ar, tr, ir, or, cr, nr, sr, lr, pr, vr, dr, gr, br, kr, ur, fr, mr, hr, Cr, xr, yr, wr, Rr, _r, Ir = {},
      Nr = 265,
      Fr = 20,
      jr = 30,
      Mr = 6,
      Ar = 5,
      Hr = 6,
      Sr = 10,
      Vr = 15,
      Tr = 180,
      Er = {
          axis: "x",
          containment: "#rev-cpicker-point-wrap"
      },
      Or = {
          color: "Color",
          solid_color: "Solid Color",
          gradient_color: "Gradient Color",
          currently_editing: "Currently Editing",
          core_presets: "Core Presets",
          custom_presets: "Custom Presets",
          enter_a_name: "Enter a Name",
          save_a_new_preset: "Save a new Preset",
          save: "Save",
          color_hex_value: "Color Hex Value",
          opacity: "Opacity",
          clear: "Clear",
          location: "Location",
          delete: "Delete",
          horizontal: "Horizontal",
          vertical: "Vertical",
          radial: "Radial",
          enter_angle: "Enter Angle",
          reverse_gradient: "Reverse Gradient",
          delete_confirm: "Remove/Delete this custom preset color?",
          naming_error: "Please enter a unique name for the new color preset"
      },
      Pr = ["#FFFFFF", "#000000", "#FF3A2D", "#007AFF", "#4CD964", "#FFCC00", "#C7C7CC", "#8E8E93", "#FFD3E0", "#34AADC", "#E0F8D8", "#FF9500"],
      zr = [{
          0: "{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:247,&g&:247,&b&:247,&a&:&1&,&position&:0,&align&:&top&},{&r&:247,&g&:247,&b&:247,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:215,&g&:215,&b&:215,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:215,&g&:215,&b&:215,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          1: "{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:74,&g&:74,&b&:74,&a&:&1&,&position&:0,&align&:&top&},{&r&:74,&g&:74,&b&:74,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:43,&g&:43,&b&:43,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:43,&g&:43,&b&:43,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          2: "{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:219,&g&:221,&b&:222,&a&:&1&,&position&:0,&align&:&top&},{&r&:219,&g&:221,&b&:222,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:137,&g&:140,&b&:144,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:137,&g&:140,&b&:144,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          3: "{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:26,&g&:214,&b&:253,&a&:&1&,&position&:0,&align&:&top&},{&r&:26,&g&:214,&b&:253,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:29,&g&:98,&b&:240,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:29,&g&:98,&b&:240,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          4: "{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:198,&g&:68,&b&:252,&a&:&1&,&position&:0,&align&:&top&},{&r&:198,&g&:68,&b&:252,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:88,&g&:86,&b&:214,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          5: "{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:94,&b&:58,&a&:&1&,&position&:0,&align&:&top&},{&r&:255,&g&:94,&b&:58,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:255,&g&:42,&b&:104,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:255,&g&:42,&b&:104,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          6: "{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:228,&g&:221,&b&:202,&a&:&1&,&position&:0,&align&:&top&},{&r&:228,&g&:221,&b&:202,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:214,&g&:206,&b&:195,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:214,&g&:206,&b&:195,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          7: "{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:219,&b&:76,&a&:&1&,&position&:0,&align&:&top&},{&r&:255,&g&:219,&b&:76,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:255,&g&:205,&b&:2,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:255,&g&:205,&b&:2,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          8: "{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:255,&g&:149,&b&:0,&a&:&1&,&position&:0,&align&:&top&},{&r&:255,&g&:149,&b&:0,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:255,&g&:94,&b&:58,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:255,&g&:94,&b&:58,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          9: "{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:82,&g&:237,&b&:199,&a&:&1&,&position&:0,&align&:&top&},{&r&:82,&g&:237,&b&:199,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:90,&g&:200,&b&:251,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:90,&g&:200,&b&:251,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          10: "{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:228,&g&:183,&b&:240,&a&:&1&,&position&:0,&align&:&top&},{&r&:228,&g&:183,&b&:240,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:200,&g&:110,&b&:223,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:200,&g&:110,&b&:223,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          11: "{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:135,&g&:252,&b&:112,&a&:&1&,&position&:0,&align&:&top&},{&r&:135,&g&:252,&b&:112,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:11,&g&:211,&b&:24,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:11,&g&:211,&b&:24,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          12: "{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:61,&g&:78,&b&:129,&a&:&1&,&position&:0,&align&:&top&},{&r&:61,&g&:78,&b&:129,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:87,&g&:83,&b&:201,&a&:&1&,&position&:50,&align&:&bottom&},{&r&:110,&g&:127,&b&:243,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:110,&g&:127,&b&:243,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          13: "{&type&:&linear&,&angle&:&160&,&colors&:[{&r&:35,&g&:21,&b&:87,&a&:&1&,&position&:0,&align&:&top&},{&r&:35,&g&:21,&b&:87,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:68,&g&:16,&b&:122,&a&:&1&,&position&:29,&align&:&bottom&},{&r&:255,&g&:19,&b&:97,&a&:&1&,&position&:67,&align&:&bottom&},{&r&:255,&g&:248,&b&:0,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:255,&g&:248,&b&:0,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          14: "{&type&:&linear&,&angle&:&160&,&colors&:[{&r&:105,&g&:234,&b&:203,&a&:&1&,&position&:0,&align&:&top&},{&r&:105,&g&:234,&b&:203,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:234,&g&:204,&b&:248,&a&:&1&,&position&:50,&align&:&bottom&},{&r&:102,&g&:84,&b&:241,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:102,&g&:84,&b&:241,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          15: "{&type&:&linear&,&angle&:&160&,&colors&:[{&r&:255,&g&:5,&b&:124,&a&:&1&,&position&:0,&align&:&top&},{&r&:255,&g&:5,&b&:124,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:124,&g&:100,&b&:213,&a&:&1&,&position&:50,&align&:&bottom&},{&r&:76,&g&:195,&b&:255,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:76,&g&:195,&b&:255,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          16: "{&type&:&linear&,&angle&:&160&,&colors&:[{&r&:255,&g&:5,&b&:124,&a&:&1&,&position&:0,&align&:&top&},{&r&:255,&g&:5,&b&:124,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:141,&g&:11,&b&:147,&a&:&1&,&position&:50,&align&:&bottom&},{&r&:50,&g&:21,&b&:117,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:50,&g&:21,&b&:117,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          17: "{&type&:&linear&,&angle&:&160&,&colors&:[{&r&:164,&g&:69,&b&:178,&a&:&1&,&position&:0,&align&:&top&},{&r&:164,&g&:69,&b&:178,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:212,&g&:24,&b&:114,&a&:&1&,&position&:50,&align&:&bottom&},{&r&:255,&g&:0,&b&:102,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:255,&g&:0,&b&:102,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          18: "{&type&:&linear&,&angle&:&160&,&colors&:[{&r&:158,&g&:251,&b&:211,&a&:&1&,&position&:0,&align&:&top&},{&r&:158,&g&:251,&b&:211,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:87,&g&:233,&b&:242,&a&:&1&,&position&:50,&align&:&bottom&},{&r&:69,&g&:212,&b&:251,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:69,&g&:212,&b&:251,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          19: "{&type&:&linear&,&angle&:&160&,&colors&:[{&r&:172,&g&:50,&b&:228,&a&:&1&,&position&:0,&align&:&top&},{&r&:172,&g&:50,&b&:228,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:121,&g&:24,&b&:242,&a&:&1&,&position&:50,&align&:&bottom&},{&r&:72,&g&:1,&b&:255,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:72,&g&:1,&b&:255,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          20: "{&type&:&linear&,&angle&:&160&,&colors&:[{&r&:112,&g&:133,&b&:182,&a&:&1&,&position&:0,&align&:&top&},{&r&:112,&g&:133,&b&:182,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:135,&g&:167,&b&:217,&a&:&1&,&position&:50,&align&:&bottom&},{&r&:222,&g&:243,&b&:248,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:222,&g&:243,&b&:248,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          21: "{&type&:&linear&,&angle&:&160&,&colors&:[{&r&:34,&g&:225,&b&:255,&a&:&1&,&position&:0,&align&:&top&},{&r&:34,&g&:225,&b&:255,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:29,&g&:143,&b&:225,&a&:&1&,&position&:50,&align&:&bottom&},{&r&:98,&g&:94,&b&:177,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:98,&g&:94,&b&:177,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          22: "{&type&:&linear&,&angle&:&160&,&colors&:[{&r&:44,&g&:216,&b&:213,&a&:&1&,&position&:0,&align&:&top&},{&r&:44,&g&:216,&b&:213,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:107,&g&:141,&b&:214,&a&:&1&,&position&:50,&align&:&bottom&},{&r&:142,&g&:55,&b&:215,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:142,&g&:55,&b&:215,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          23: "{&type&:&linear&,&angle&:&160&,&colors&:[{&r&:44,&g&:216,&b&:213,&a&:&1&,&position&:0,&align&:&top&},{&r&:44,&g&:216,&b&:213,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:197,&g&:193,&b&:255,&a&:&1&,&position&:56,&align&:&bottom&},{&r&:255,&g&:186,&b&:195,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:255,&g&:186,&b&:195,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          24: "{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:191,&g&:217,&b&:254,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:191,&g&:217,&b&:254,&a&:&1&,&position&:0,&align&:&top&},{&r&:223,&g&:137,&b&:181,&a&:&1&,&position&:100,&align&:&top&},{&r&:223,&g&:137,&b&:181,&a&:&1&,&position&:100,&align&:&bottom&}]}"
      }, {
          25: "{&type&:&linear&,&angle&:&340&,&colors&:[{&r&:97,&g&:97,&b&:97,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:97,&g&:97,&b&:97,&a&:&1&,&position&:0,&align&:&top&},{&r&:155,&g&:197,&b&:195,&a&:&1&,&position&:100,&align&:&top&},{&r&:155,&g&:197,&b&:195,&a&:&1&,&position&:100,&align&:&bottom&}]}"
      }, {
          26: "{&type&:&linear&,&angle&:&90&,&colors&:[{&r&:36,&g&:57,&b&:73,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:36,&g&:57,&b&:73,&a&:&1&,&position&:0,&align&:&top&},{&r&:81,&g&:127,&b&:164,&a&:&1&,&position&:100,&align&:&top&},{&r&:81,&g&:127,&b&:164,&a&:&1&,&position&:100,&align&:&bottom&}]}"
      }, {
          27: "{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:234,&g&:205,&b&:163,&a&:&1&,&position&:0,&align&:&top&},{&r&:234,&g&:205,&b&:163,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:230,&g&:185,&b&:128,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:230,&g&:185,&b&:128,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          28: "{&type&:&linear&,&angle&:&45&,&colors&:[{&r&:238,&g&:156,&b&:167,&a&:&1&,&position&:0,&align&:&top&},{&r&:238,&g&:156,&b&:167,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:255,&g&:221,&b&:225,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:255,&g&:221,&b&:225,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          29: "{&type&:&linear&,&angle&:&340&,&colors&:[{&r&:247,&g&:148,&b&:164,&a&:&1&,&position&:0,&align&:&top&},{&r&:247,&g&:148,&b&:164,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:253,&g&:214,&b&:189,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:253,&g&:214,&b&:189,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          30: "{&type&:&linear&,&angle&:&45&,&colors&:[{&r&:135,&g&:77,&b&:162,&a&:&1&,&position&:0,&align&:&top&},{&r&:135,&g&:77,&b&:162,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:196,&g&:58,&b&:48,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          31: "{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:243,&g&:231,&b&:233,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:243,&g&:231,&b&:233,&a&:&1&,&position&:0,&align&:&top&},{&r&:218,&g&:212,&b&:236,&a&:&1&,&position&:100,&align&:&top&},{&r&:218,&g&:212,&b&:236,&a&:&1&,&position&:100,&align&:&bottom&}]}"
      }, {
          32: "{&type&:&linear&,&angle&:&320&,&colors&:[{&r&:43,&g&:88,&b&:118,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:43,&g&:88,&b&:118,&a&:&1&,&position&:0,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:&1&,&position&:100,&align&:&top&},{&r&:78,&g&:67,&b&:118,&a&:&1&,&position&:100,&align&:&bottom&}]}"
      }, {
          33: "{&type&:&linear&,&angle&:&60&,&colors&:[{&r&:41,&g&:50,&b&:60,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:41,&g&:50,&b&:60,&a&:&1&,&position&:0,&align&:&top&},{&r&:72,&g&:85,&b&:99,&a&:&1&,&position&:100,&align&:&top&},{&r&:72,&g&:85,&b&:99,&a&:&1&,&position&:100,&align&:&bottom&}]}"
      }, {
          34: "{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:233,&g&:233,&b&:231,&a&:&1&,&position&:0,&align&:&top&},{&r&:233,&g&:233,&b&:231,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:239,&g&:238,&b&:236,&a&:&1&,&position&:25,&align&:&bottom&},{&r&:238,&g&:238,&b&:238,&a&:&1&,&position&:70,&align&:&bottom&},{&r&:213,&g&:212,&b&:208,&a&:&1&,&position&:100,&align&:&bottom&},{&r&:213,&g&:212,&b&:208,&a&:&1&,&position&:100,&align&:&top&}]}"
      }, {
          35: "{&type&:&linear&,&angle&:&180&,&colors&:[{&r&:251,&g&:200,&b&:212,&a&:&1&,&position&:0,&align&:&bottom&},{&r&:251,&g&:200,&b&:212,&a&:&1&,&position&:0,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:&1&,&position&:100,&align&:&top&},{&r&:151,&g&:149,&b&:240,&a&:&1&,&position&:100,&align&:&bottom&}]}"
      }];
  e.tpColorPicker = function(r) {
      Z || (Z = e("body"), ce = e('<div class="rev-cpicker-wrap color-view"><div id="rev-cpicker-back" class="rev-cpicker-close"></div><div id="rev-cpicker"><div id="rev-cpicker-head"><div id="rev-cpicker-drag" class="rev-cpicker-draggable"></div><span id="rev-cpicker-color-btn" class="rev-cpicker-main-btn" data-text="solid_color"></span><span id="rev-cpicker-gradient-btn" class="rev-cpicker-main-btn" data-text="gradient_color"></span><div id="rev-cpicker-editing" class="rev-cpicker-draggable"><span id="rev-cpicker-edit-title" data-text="currently_editing"></span><span id="rev-cpicker-current-edit"></span></div><span id="rev-cpicker-exit" class="rev-cpicker-close"></span></div><div id="rev-cpicker-section-left" class="rev-cpicker-section"><div id="rev-cpicker-body"><div id="rev-cpicker-colors" class="rev-cpicker-type"><div class="rev-cpicker-column rev-cpicker-column-left">\t<div class="rev-cpicker-column-inner-left"><div class="rev-cpicker-presets"><span id="rev-cpicker-colors-core-btn" class="rev-cpicker-preset-title rev-cpicker-preset-title-core selected"><span data-text="core_presets"></span> <span class="rev-cpicker-arrow rev-cpicker-arrow-down"></span><span class="rev-cpicker-arrow rev-cpicker-arrow-up"></span></span><span id="rev-cpicker-colors-custom-btn" class="rev-cpicker-preset-title rev-cpicker-preset-title-custom"><span data-text="custom_presets"></span> <span class="rev-cpicker-arrow rev-cpicker-arrow-down"></span><span class="rev-cpicker-arrow rev-cpicker-arrow-up"></span></span><div id="rev-cpicker-colors-core" class="rev-cpicker-presets-group"></div><div id="rev-cpicker-colors-custom" class="rev-cpicker-presets-group rev-cpicker-presets-custom"></div></div><div class="rev-cpicker-iris"><input id="rev-cpicker-iris-color" class="rev-cpicker-iris-input" value="#ffffff" /><div id="rev-cpicker-scroller" class="iris-slider iris-strip"><div id="rev-cpicker-scroll-bg"></div><div id="rev-cpicker-scroll" class="iris-slider-offset"></div></div></div></div></div><div class="rev-cpicker-column rev-cpicker-column-right"><div class="rev-cpicker-column-inner-right"><div><span data-text="save_a_new_preset"></span><div class="rev-cpicker-presets-save-as"><input type="text" class="rev-cpicker-preset-save" placeholder="" data-placeholder="enter_a_name" /><span class="rev-cpicker-btn rev-cpicker-save-preset-btn" data-alert="naming_error"><span class="rev-cpicker-save-icon"></span><span class="rev-cpicker-preset-save-text" data-text="save"></span></span></div></div><div class="rev-cpicker-meta"><span data-text="color_hex_value"></span><br><input type="text" id="rev-cpicker-color-hex" class="rev-cpicker-input rev-cpicker-hex" value="#ffffff" /><br><span data-text="opacity" class="rev-cpicker-hideable"></span><br><input type="text" id="rev-cpicker-color-opacity" class="rev-cpicker-input rev-cpicker-opacity-input rev-cpicker-hideable" value="100%" /><span id="rev-cpciker-clear-hex" class="rev-cpicker-btn rev-cpicker-btn-small rev-cpciker-clear rev-cpicker-hideable" data-text="clear"></span></div></div></div></div><div id="rev-cpicker-gradients" class="rev-cpicker-type"><div class="rev-cpicker-column rev-cpicker-column-left">\t<div class="rev-cpicker-column-inner-left"><div class="rev-cpicker-presets"><span id="rev-cpicker-gradients-core-btn" class="rev-cpicker-preset-title rev-cpicker-preset-title-core selected"><span data-text="core_presets"></span> <span class="rev-cpicker-arrow rev-cpicker-arrow-down"></span><span class="rev-cpicker-arrow rev-cpicker-arrow-up"></span></span><span id="rev-cpicker-gradients-custom-btn" class="rev-cpicker-preset-title rev-cpicker-preset-title-custom"><span data-text="custom_presets"></span> <span class="rev-cpicker-arrow rev-cpicker-arrow-down"></span><span class="rev-cpicker-arrow rev-cpicker-arrow-up"></span></span><div id="rev-cpicker-gradients-core" class="rev-cpicker-presets-group"></div><div id="rev-cpicker-gradients-custom" class="rev-cpicker-presets-group rev-cpicker-presets-custom"></div></div><div class="rev-cpicker-gradient-block"><div id="rev-cpicker-gradient-input" class="rev-cpicker-gradient-builder"><span id="rev-cpicker-hit-top" class="rev-cpicker-builder-hit"></span><div id="rev-cpicker-point-wrap"><div id="rev-cpciker-point-container"></div></div><span id="rev-cpicker-hit-bottom" class="rev-cpicker-builder-hit"></span></div><div class="rev-cpicker-meta-row-wrap"><div class="rev-cpicker-meta-row"><div><label data-text="opacity"></label><input type="text" id="rev-cpicker-grad-opacity" class="rev-cpicker-point-input rev-cpicker-opacity-input" value="100%" disabled /></div><div><label data-text="location"></label><input type="text" id="rev-cpicker-opacity-location" class="rev-cpicker-point-input rev-cpicker-point-location" value="100%" disabled /></div><div><label>&nbsp;</label><span class="rev-cpicker-btn rev-cpicker-btn-small rev-cpicker-point-delete" id="rev-cpicker-opacity-delete" data-text="delete">{{delete}}</span></div></div><div class="rev-cpicker-meta-row"><div><label data-text="color"></label><span class="rev-cpicker-point-input" id="rev-cpicker-color-box"></span></div><div><label data-text="location"></label><input type="text" id="rev-cpicker-color-location" class="rev-cpicker-point-input rev-cpicker-point-location" value="100%" disabled /></div><div><label>&nbsp;</label><span class="rev-cpicker-btn rev-cpicker-btn-small rev-cpicker-point-input rev-cpicker-point-delete" id="rev-cpicker-color-delete" data-text="delete">{{delete}}</span></div></div></div></div></div></div><div class="rev-cpicker-column rev-cpicker-column-right"><div class="rev-cpicker-column-inner-right"><div><span data-text="save_a_new_preset"></span><div class="rev-cpicker-presets-save-as"><input type="text" class="rev-cpicker-preset-save" placeholder="" data-placeholder="enter_a_name" /><span class="rev-cpicker-btn rev-cpicker-save-preset-btn" data-alert="naming_error"><span class="rev-cpicker-save-icon"></span><span class="rev-cpicker-preset-save-text" data-text="save"></span></span></div></div><div class="rev-cpicker-gradient-block"><div id="rev-cpicker-gradient-output" class="rev-cpicker-gradient-builder"></div></div><div class="rev-cpicker-meta-row-wrap"><div class="rev-cpicker-meta-row"><div><label>Orientation</label><span id="rev-cpicker-orientation-horizontal" class="rev-cpicker-btn rev-cpicker-btn-small rev-cpicker-orientation" data-direction="90" data-text="horizontal"></span><span id="rev-cpicker-orientation-vertical" class="rev-cpicker-btn rev-cpicker-btn-small rev-cpicker-orientation" data-direction="180" data-text="vertical"></span><span id="rev-cpicker-orientation-radial" class="rev-cpicker-btn rev-cpicker-btn-small rev-cpicker-orientation" data-direction="radial" data-text="radial"></span></div></div><div class="rev-cpicker-meta-row rev-cpicker-meta-row-push"><div><label data-text="enter_angle"></label><div id="rev-cpicker-angle-container"><input type="text" class="rev-cpicker-input" id="rev-cpicker-meta-angle" value="" /><div id="rev-cpicker-wheel"><div id="rev-cpicker-wheel-inner"><span id="rev-cpicker-wheel-point"></span></div></div></div></div><div><label data-text="reverse_gradient"></label><span id="rev-cpicker-meta-reverse"></span></div></div></div></div></div></div></div></div><span id="rev-cpicker-check"></span><div id="rev-cpicker-section-right" class="rev-cpicker-section"><div class="rev-cpicker-iris"><input id="rev-cpicker-iris-gradient" class="rev-cpicker-iris-input" value="#ffffff" /></div><div class="rev-cpicker-fields"><input type="text" id="rev-cpicker-gradient-hex" class="rev-cpicker-input rev-cpicker-hex" value="#ffffff" /><span id="rev-cpciker-clear-gradient" class="rev-cpicker-btn rev-cpicker-btn-small rev-cpciker-clear" data-text="clear"></span><span id="rev-cpicker-check-gradient" class="rev-cpicker-btn"></span></div></div><span id="rev-cpicker-remove-delete" data-text="delete_confirm"></span></div></div>').appendTo(Z)), r || (r = {}), r.core && (r.core.colors && (Pr = r.core.colors), r.core.gradients && (zr = r.core.gradients)), Y(r), ne ? (cr.perfectScrollbar("update"), r.mode && (ve = r.mode), r.language && n(r.language)) : (n(r.language || Or), ve = r.mode || "full"), r.init && (ie = r.init), r.onAjax && (be = r.onAjax), r.onEdit && (ge = r.onEdit), r.change && (Ie = r.change), r.cancel && (_e = r.cancel), r.widgetId && (tr = r.widgetId), r.defaultValue && (RevColor.defaultValue = r.defaultValue), r.wrapClasses && (Rr = r.wrapClasses), r.appendedHtml && (vr = r.appendedHtml), ne = !0
  };
  var Dr = {
      refresh: function() {
          var r = e(this);
          if (r.hasClass("rev-cpicker-component")) {
              var a = r.data("revcp") || {},
                  t = r.val() || a.defaultValue || RevColor.defaultValue,
                  i = RevColor.process(t);
              t = i[0], i = "rgba" === i[1] && RevColor.transparentRgba(t, !0) ? "" : t, "transparent" !== t ? r.data("tpcp")[0].style.background = i : r.data("tpcp").css("background", ""), r.attr("data-color", t).data("hex", t)
          }
      },
      destroy: function() {
          e(this).removeData().closest(".rev-cpicker-master-wrap").removeData().remove()
      }
  };
  e.fn.tpColorPicker = function(r) {
      return r && "string" == typeof r ? this.each(Dr[r]) : this.each(function() {
          var a = e(this);
          if (a.hasClass("rev-cpicker-component")) a.tpColorPicker("refresh");
          else {
              var t, i, o = e('<span class="rev-colorpicker"></span>').data("revcolorinput", a),
                  c = e('<span class="rev-colorbox" />'),
                  n = e('<span class="rev-colorbtn" />'),
                  s = a.attr("data-wrap-classes"),
                  l = a.attr("data-wrapper"),
                  p = a.attr("data-wrap-id"),
                  v = a.attr("data-title"),
                  d = a.attr("data-skin"),
                  g = a.val() || "",
                  b = W(g);
              if (b && (g = b, a.val(b)), o.insertBefore(a).append([c, n, a]), r && e.isPlainObject(r)) {
                  l || (l = r.wrapper), s || (s = r.wrapClasses), d || (d = r.skin), p || (p = r.wrapId), v || (v = r.title), i = r.defaultValue;
                  var k = a.data("revcp");
                  k && (r = e.extend({}, k, r)), a.data("revcp", r)
              }
              s || (s = Rr), s && o.addClass(s), p && o.attr("id", p), g || (g = i || RevColor.defaultValue, a.val(g)), g = (t = RevColor.process(g))[0], 3 === t.length && a.val(g), "transparent" !== (t = "rgba" === t[1] && RevColor.transparentRgba(g, !0) ? "" : g) && (c[0].style.background = t), n[0].innerHTML = v || Te || Or.color, a.attr({
                  type: "hidden",
                  "data-color": g
              }).data("tpcp", c).addClass("rev-cpicker-component"), d && o.addClass(d), l ? (l = e(l).addClass("rev-cpicker-master-wrap"), o.wrap(l)) : o.addClass("rev-cpicker-master-wrap");
              var u = !!r && (r.init || ie);
              u && u(o, a, g, r)
          }
      })
  }, e(function() {
      e("body").on("click.revcpicker", ".rev-colorpicker", function() {
          U || J();
          var r, a, t, i, o, c, s, p, v, g, b, k, u, f = (or = e(this).data("revcolorinput")).attr("data-widget-id"),
              m = or.attr("data-appended-html"),
              h = or.attr("data-editing"),
              C = or.attr("data-colors"),
              x = or.attr("data-mode"),
              y = or.data("revcp"),
              R = or.attr("data-lang");
          if (C && ((C = JSON.parse(C.replace(/\&/g, '"'))).colors && (c = C.colors), C.gradients && (i = C.gradients)), y) {
              var _ = y.colors;
              _ && (_.core && (t = _.core.colors, r = _.core.gradients), _.custom && (o = _.custom.colors, a = _.custom.gradients)), b = y.onEdit, k = y.onAjax, p = y.change, v = y.cancel, R || (R = y.lang), x || (x = y.mode), m || (m = y.appendedHtml), h || (h = y.editing), f || (f = y.widgetId)
          }(r || t || a || o || i || c) && (s = {}, (r || t || i || c) && (s.core = {
              colors: c || t || Pr,
              gradients: i || r || zr
          }), (a || o) && (s.custom = {
              colors: o || Pr,
              gradients: a || zr
          }), Y(s)), f || (f = tr), f && (ce[0].id = f), m || (m = vr), m && (ye = e(m).appendTo(mr)), R && n(R), x || (x = ve), h ? ze.style.visibility = "visible" : (h = "", ze.style.visibility = "hidden"), wr.innerHTML = h, "single" === x || "basic" === x ? (K = !1, le.hide(), ue.show(), "basic" === x && ce.addClass("is-basic")) : (K = !0, le.show(), ue.show()), (u = or.val() || or.attr("data-color") || RevColor.defaultValue).split("||").length > 1 && (u = RevColor.joinToRgba(u), or.val(u)), g = d(u), lr = g[0], te = b || ge, oe = k || be, Re = v || _e, _r = p || Ie, "gradient" !== g[1] ? ue.data("state", lr) : le.data("state", lr), Z.addClass("rev-colorpicker-open"), dr = or.data("tpcp"), ce.data("revcpickerinput", or).addClass("active").show(), cr.each(w).perfectScrollbar("update"), pr = or.attr("data-color"), ir = or.data("hex"), e(".rev-cpicker-color").not(".blank").each(l)
      })
  })
}("undefined" !== jQuery && jQuery);