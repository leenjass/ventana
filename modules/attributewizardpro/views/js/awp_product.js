/* Connected attributes */
var BrowserDetect = {
    init: function () {
        this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
        this.version = this.searchVersion(navigator.userAgent)
            || this.searchVersion(navigator.appVersion)
            || "an unknown version";
        this.OS = this.searchString(this.dataOS) || "an unknown OS";
    },
    searchString: function (data) {
        for (var i = 0; i < data.length; i++) {
            var dataString = data[i].string;
            var dataProp = data[i].prop;
            this.versionSearchString = data[i].versionSearch || data[i].identity;
            if (dataString) {
                if (dataString.indexOf(data[i].subString) != -1)
                    return data[i].identity;
            } else if (dataProp)
                return data[i].identity;
        }
    },
    searchVersion: function (dataString) {
        var index = dataString.indexOf(this.versionSearchString);
        if (index == -1)
            return;
        return parseFloat(dataString.substring(index + this.versionSearchString.length + 1));
    },
    dataBrowser: [
        {
            string: navigator.userAgent,
            subString: "Chrome",
            identity: "Chrome"
        },
        {string: navigator.userAgent,
            subString: "OmniWeb",
            versionSearch: "OmniWeb/",
            identity: "OmniWeb"
        },
        {
            string: navigator.vendor,
            subString: "Apple",
            identity: "Safari",
            versionSearch: "Version"
        },
        {
            prop: window.opera,
            identity: "Opera"
        },
        {
            string: navigator.vendor,
            subString: "iCab",
            identity: "iCab"
        },
        {
            string: navigator.vendor,
            subString: "KDE",
            identity: "Konqueror"
        },
        {
            string: navigator.userAgent,
            subString: "Firefox",
            identity: "Firefox"
        },
        {
            string: navigator.vendor,
            subString: "Camino",
            identity: "Camino"
        },
        {
            string: navigator.userAgent,
            subString: "Netscape",
            identity: "Netscape"
        },
        {
            string: navigator.userAgent,
            subString: "MSIE",
            identity: "Explorer",
            versionSearch: "MSIE"
        },
        {
            string: navigator.userAgent,
            subString: "Gecko",
            identity: "Mozilla",
            versionSearch: "rv"
        },
        {
            string: navigator.userAgent,
            subString: "Mozilla",
            identity: "Netscape",
            versionSearch: "Mozilla"
        }
    ],
    dataOS: [
        {
            string: navigator.platform,
            subString: "Win",
            identity: "Windows"
        },
        {
            string: navigator.platform,
            subString: "Mac",
            identity: "Mac"
        },
        {
            string: navigator.userAgent,
            subString: "iPhone",
            identity: "iPhone/iPod"
        },
        {
            string: navigator.platform,
            subString: "Linux",
            identity: "Linux"
        }
    ]

};
BrowserDetect.init();
var isIE = BrowserDetect.browser != "Mozilla" ? false : true;
/* End Connected attributes */

/* Helper functions from PS 1.5+ */
Number.prototype.formatMoney = function (places, symbol, thousand, decimal, posSymbol) {
    places = !isNaN(places = Math.abs(places)) ? places : 2;
    symbol = symbol !== undefined ? symbol : "$";
    thousand = thousand || "";
    decimal = decimal || ".";
    var number = this,
        negative = number < 0 ? "-" : "",
        i = parseInt(number = Math.abs(+number || 0).toFixed(places), 10) + "",
        j = (j = i.length) > 3 ? j % 3 : 0;
    return (posSymbol == 0 ? symbol : '') + negative + (j ? i.substr(0, j) + thousand : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousand) + (places ? decimal + Math.abs(number - i).toFixed(places).slice(2) : "") + (posSymbol > 0 ? symbol : '');
};

function url_decode(str) {
    return unescape(str.replace(/\+/g, " "));
}
Encoder = {
    /* When encoding do we convert characters into html or numerical entities */
    EncodeType: "entity", /* entity OR numerical*/

    isEmpty: function (val) {
        if (val) {
            return ((val === null) || val.length == 0 || /^\s+$/.test(val));
        } else {
            return true;
        }
    },
    /* Convert HTML entities into numerical entities */
    HTML2Numerical: function (s) {
        var arr1 = new Array('&nbsp;', '&iexcl;', '&cent;', '&pound;', '&curren;', '&yen;', '&brvbar;', '&sect;', '&uml;', '&copy;', '&ordf;', '&laquo;', '&not;', '&shy;', '&reg;', '&macr;', '&deg;', '&plusmn;', '&sup2;', '&sup3;', '&acute;', '&micro;', '&para;', '&middot;', '&cedil;', '&sup1;', '&ordm;', '&raquo;', '&frac14;', '&frac12;', '&frac34;', '&iquest;', '&agrave;', '&aacute;', '&acirc;', '&atilde;', '&Auml;', '&aring;', '&aelig;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&igrave;', '&iacute;', '&icirc;', '&iuml;', '&eth;', '&ntilde;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&Ouml;', '&times;', '&oslash;', '&ugrave;', '&uacute;', '&ucirc;', '&Uuml;', '&yacute;', '&thorn;', '&szlig;', '&agrave;', '&aacute;', '&acirc;', '&atilde;', '&auml;', '&aring;', '&aelig;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&igrave;', '&iacute;', '&icirc;', '&iuml;', '&eth;', '&ntilde;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&ouml;', '&divide;', '&oslash;', '&ugrave;', '&uacute;', '&ucirc;', '&uuml;', '&yacute;', '&thorn;', '&yuml;', '&quot;', '&amp;', '&lt;', '&gt;', '&oelig;', '&oelig;', '&scaron;', '&scaron;', '&yuml;', '&circ;', '&tilde;', '&ensp;', '&emsp;', '&thinsp;', '&zwnj;', '&zwj;', '&lrm;', '&rlm;', '&ndash;', '&mdash;', '&lsquo;', '&rsquo;', '&sbquo;', '&ldquo;', '&rdquo;', '&bdquo;', '&dagger;', '&dagger;', '&permil;', '&lsaquo;', '&rsaquo;', '&euro;', '&fnof;', '&alpha;', '&beta;', '&gamma;', '&delta;', '&epsilon;', '&zeta;', '&eta;', '&theta;', '&iota;', '&kappa;', '&lambda;', '&mu;', '&nu;', '&xi;', '&omicron;', '&pi;', '&rho;', '&sigma;', '&tau;', '&upsilon;', '&phi;', '&chi;', '&psi;', '&omega;', '&alpha;', '&beta;', '&gamma;', '&delta;', '&epsilon;', '&zeta;', '&eta;', '&theta;', '&iota;', '&kappa;', '&lambda;', '&mu;', '&nu;', '&xi;', '&omicron;', '&pi;', '&rho;', '&sigmaf;', '&sigma;', '&tau;', '&upsilon;', '&phi;', '&chi;', '&psi;', '&omega;', '&thetasym;', '&upsih;', '&piv;', '&bull;', '&hellip;', '&prime;', '&prime;', '&oline;', '&frasl;', '&weierp;', '&image;', '&real;', '&trade;', '&alefsym;', '&larr;', '&uarr;', '&rarr;', '&darr;', '&harr;', '&crarr;', '&larr;', '&uarr;', '&rarr;', '&darr;', '&harr;', '&forall;', '&part;', '&exist;', '&empty;', '&nabla;', '&isin;', '&notin;', '&ni;', '&prod;', '&sum;', '&minus;', '&lowast;', '&radic;', '&prop;', '&infin;', '&ang;', '&and;', '&or;', '&cap;', '&cup;', '&int;', '&there4;', '&sim;', '&cong;', '&asymp;', '&ne;', '&equiv;', '&le;', '&ge;', '&sub;', '&sup;', '&nsub;', '&sube;', '&supe;', '&oplus;', '&otimes;', '&perp;', '&sdot;', '&lceil;', '&rceil;', '&lfloor;', '&rfloor;', '&lang;', '&rang;', '&loz;', '&spades;', '&clubs;', '&hearts;', '&diams;');
        var arr2 = new Array('&#160;', '&#161;', '&#162;', '&#163;', '&#164;', '&#165;', '&#166;', '&#167;', '&#168;', '&#169;', '&#170;', '&#171;', '&#172;', '&#173;', '&#174;', '&#175;', '&#176;', '&#177;', '&#178;', '&#179;', '&#180;', '&#181;', '&#182;', '&#183;', '&#184;', '&#185;', '&#186;', '&#187;', '&#188;', '&#189;', '&#190;', '&#191;', '&#192;', '&#193;', '&#194;', '&#195;', '&#196;', '&#197;', '&#198;', '&#199;', '&#200;', '&#201;', '&#202;', '&#203;', '&#204;', '&#205;', '&#206;', '&#207;', '&#208;', '&#209;', '&#210;', '&#211;', '&#212;', '&#213;', '&#214;', '&#215;', '&#216;', '&#217;', '&#218;', '&#219;', '&#220;', '&#221;', '&#222;', '&#223;', '&#224;', '&#225;', '&#226;', '&#227;', '&#228;', '&#229;', '&#230;', '&#231;', '&#232;', '&#233;', '&#234;', '&#235;', '&#236;', '&#237;', '&#238;', '&#239;', '&#240;', '&#241;', '&#242;', '&#243;', '&#244;', '&#245;', '&#246;', '&#247;', '&#248;', '&#249;', '&#250;', '&#251;', '&#252;', '&#253;', '&#254;', '&#255;', '&#34;', '&#38;', '&#60;', '&#62;', '&#338;', '&#339;', '&#352;', '&#353;', '&#376;', '&#710;', '&#732;', '&#8194;', '&#8195;', '&#8201;', '&#8204;', '&#8205;', '&#8206;', '&#8207;', '&#8211;', '&#8212;', '&#8216;', '&#8217;', '&#8218;', '&#8220;', '&#8221;', '&#8222;', '&#8224;', '&#8225;', '&#8240;', '&#8249;', '&#8250;', '&#8364;', '&#402;', '&#913;', '&#914;', '&#915;', '&#916;', '&#917;', '&#918;', '&#919;', '&#920;', '&#921;', '&#922;', '&#923;', '&#924;', '&#925;', '&#926;', '&#927;', '&#928;', '&#929;', '&#931;', '&#932;', '&#933;', '&#934;', '&#935;', '&#936;', '&#937;', '&#945;', '&#946;', '&#947;', '&#948;', '&#949;', '&#950;', '&#951;', '&#952;', '&#953;', '&#954;', '&#955;', '&#956;', '&#957;', '&#958;', '&#959;', '&#960;', '&#961;', '&#962;', '&#963;', '&#964;', '&#965;', '&#966;', '&#967;', '&#968;', '&#969;', '&#977;', '&#978;', '&#982;', '&#8226;', '&#8230;', '&#8242;', '&#8243;', '&#8254;', '&#8260;', '&#8472;', '&#8465;', '&#8476;', '&#8482;', '&#8501;', '&#8592;', '&#8593;', '&#8594;', '&#8595;', '&#8596;', '&#8629;', '&#8656;', '&#8657;', '&#8658;', '&#8659;', '&#8660;', '&#8704;', '&#8706;', '&#8707;', '&#8709;', '&#8711;', '&#8712;', '&#8713;', '&#8715;', '&#8719;', '&#8721;', '&#8722;', '&#8727;', '&#8730;', '&#8733;', '&#8734;', '&#8736;', '&#8743;', '&#8744;', '&#8745;', '&#8746;', '&#8747;', '&#8756;', '&#8764;', '&#8773;', '&#8776;', '&#8800;', '&#8801;', '&#8804;', '&#8805;', '&#8834;', '&#8835;', '&#8836;', '&#8838;', '&#8839;', '&#8853;', '&#8855;', '&#8869;', '&#8901;', '&#8968;', '&#8969;', '&#8970;', '&#8971;', '&#9001;', '&#9002;', '&#9674;', '&#9824;', '&#9827;', '&#9829;', '&#9830;');
        return this.swapArrayVals(s, arr1, arr2);
    },
    /* Convert Numerical entities into HTML entities */
    NumericalToHTML: function (s) {
        var arr1 = new Array('&#160;', '&#161;', '&#162;', '&#163;', '&#164;', '&#165;', '&#166;', '&#167;', '&#168;', '&#169;', '&#170;', '&#171;', '&#172;', '&#173;', '&#174;', '&#175;', '&#176;', '&#177;', '&#178;', '&#179;', '&#180;', '&#181;', '&#182;', '&#183;', '&#184;', '&#185;', '&#186;', '&#187;', '&#188;', '&#189;', '&#190;', '&#191;', '&#192;', '&#193;', '&#194;', '&#195;', '&#196;', '&#197;', '&#198;', '&#199;', '&#200;', '&#201;', '&#202;', '&#203;', '&#204;', '&#205;', '&#206;', '&#207;', '&#208;', '&#209;', '&#210;', '&#211;', '&#212;', '&#213;', '&#214;', '&#215;', '&#216;', '&#217;', '&#218;', '&#219;', '&#220;', '&#221;', '&#222;', '&#223;', '&#224;', '&#225;', '&#226;', '&#227;', '&#228;', '&#229;', '&#230;', '&#231;', '&#232;', '&#233;', '&#234;', '&#235;', '&#236;', '&#237;', '&#238;', '&#239;', '&#240;', '&#241;', '&#242;', '&#243;', '&#244;', '&#245;', '&#246;', '&#247;', '&#248;', '&#249;', '&#250;', '&#251;', '&#252;', '&#253;', '&#254;', '&#255;', '&#34;', '&#38;', '&#60;', '&#62;', '&#338;', '&#339;', '&#352;', '&#353;', '&#376;', '&#710;', '&#732;', '&#8194;', '&#8195;', '&#8201;', '&#8204;', '&#8205;', '&#8206;', '&#8207;', '&#8211;', '&#8212;', '&#8216;', '&#8217;', '&#8218;', '&#8220;', '&#8221;', '&#8222;', '&#8224;', '&#8225;', '&#8240;', '&#8249;', '&#8250;', '&#8364;', '&#402;', '&#913;', '&#914;', '&#915;', '&#916;', '&#917;', '&#918;', '&#919;', '&#920;', '&#921;', '&#922;', '&#923;', '&#924;', '&#925;', '&#926;', '&#927;', '&#928;', '&#929;', '&#931;', '&#932;', '&#933;', '&#934;', '&#935;', '&#936;', '&#937;', '&#945;', '&#946;', '&#947;', '&#948;', '&#949;', '&#950;', '&#951;', '&#952;', '&#953;', '&#954;', '&#955;', '&#956;', '&#957;', '&#958;', '&#959;', '&#960;', '&#961;', '&#962;', '&#963;', '&#964;', '&#965;', '&#966;', '&#967;', '&#968;', '&#969;', '&#977;', '&#978;', '&#982;', '&#8226;', '&#8230;', '&#8242;', '&#8243;', '&#8254;', '&#8260;', '&#8472;', '&#8465;', '&#8476;', '&#8482;', '&#8501;', '&#8592;', '&#8593;', '&#8594;', '&#8595;', '&#8596;', '&#8629;', '&#8656;', '&#8657;', '&#8658;', '&#8659;', '&#8660;', '&#8704;', '&#8706;', '&#8707;', '&#8709;', '&#8711;', '&#8712;', '&#8713;', '&#8715;', '&#8719;', '&#8721;', '&#8722;', '&#8727;', '&#8730;', '&#8733;', '&#8734;', '&#8736;', '&#8743;', '&#8744;', '&#8745;', '&#8746;', '&#8747;', '&#8756;', '&#8764;', '&#8773;', '&#8776;', '&#8800;', '&#8801;', '&#8804;', '&#8805;', '&#8834;', '&#8835;', '&#8836;', '&#8838;', '&#8839;', '&#8853;', '&#8855;', '&#8869;', '&#8901;', '&#8968;', '&#8969;', '&#8970;', '&#8971;', '&#9001;', '&#9002;', '&#9674;', '&#9824;', '&#9827;', '&#9829;', '&#9830;');
        var arr2 = new Array('&nbsp;', '&iexcl;', '&cent;', '&pound;', '&curren;', '&yen;', '&brvbar;', '&sect;', '&uml;', '&copy;', '&ordf;', '&laquo;', '&not;', '&shy;', '&reg;', '&macr;', '&deg;', '&plusmn;', '&sup2;', '&sup3;', '&acute;', '&micro;', '&para;', '&middot;', '&cedil;', '&sup1;', '&ordm;', '&raquo;', '&frac14;', '&frac12;', '&frac34;', '&iquest;', '&agrave;', '&aacute;', '&acirc;', '&atilde;', '&Auml;', '&aring;', '&aelig;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&igrave;', '&iacute;', '&icirc;', '&iuml;', '&eth;', '&ntilde;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&Ouml;', '&times;', '&oslash;', '&ugrave;', '&uacute;', '&ucirc;', '&Uuml;', '&yacute;', '&thorn;', '&szlig;', '&agrave;', '&aacute;', '&acirc;', '&atilde;', '&auml;', '&aring;', '&aelig;', '&ccedil;', '&egrave;', '&eacute;', '&ecirc;', '&euml;', '&igrave;', '&iacute;', '&icirc;', '&iuml;', '&eth;', '&ntilde;', '&ograve;', '&oacute;', '&ocirc;', '&otilde;', '&ouml;', '&divide;', '&oslash;', '&ugrave;', '&uacute;', '&ucirc;', '&uuml;', '&yacute;', '&thorn;', '&yuml;', '&quot;', '&amp;', '&lt;', '&gt;', '&oelig;', '&oelig;', '&scaron;', '&scaron;', '&yuml;', '&circ;', '&tilde;', '&ensp;', '&emsp;', '&thinsp;', '&zwnj;', '&zwj;', '&lrm;', '&rlm;', '&ndash;', '&mdash;', '&lsquo;', '&rsquo;', '&sbquo;', '&ldquo;', '&rdquo;', '&bdquo;', '&dagger;', '&dagger;', '&permil;', '&lsaquo;', '&rsaquo;', '&euro;', '&fnof;', '&alpha;', '&beta;', '&gamma;', '&delta;', '&epsilon;', '&zeta;', '&eta;', '&theta;', '&iota;', '&kappa;', '&lambda;', '&mu;', '&nu;', '&xi;', '&omicron;', '&pi;', '&rho;', '&sigma;', '&tau;', '&upsilon;', '&phi;', '&chi;', '&psi;', '&omega;', '&alpha;', '&beta;', '&gamma;', '&delta;', '&epsilon;', '&zeta;', '&eta;', '&theta;', '&iota;', '&kappa;', '&lambda;', '&mu;', '&nu;', '&xi;', '&omicron;', '&pi;', '&rho;', '&sigmaf;', '&sigma;', '&tau;', '&upsilon;', '&phi;', '&chi;', '&psi;', '&omega;', '&thetasym;', '&upsih;', '&piv;', '&bull;', '&hellip;', '&prime;', '&prime;', '&oline;', '&frasl;', '&weierp;', '&image;', '&real;', '&trade;', '&alefsym;', '&larr;', '&uarr;', '&rarr;', '&darr;', '&harr;', '&crarr;', '&larr;', '&uarr;', '&rarr;', '&darr;', '&harr;', '&forall;', '&part;', '&exist;', '&empty;', '&nabla;', '&isin;', '&notin;', '&ni;', '&prod;', '&sum;', '&minus;', '&lowast;', '&radic;', '&prop;', '&infin;', '&ang;', '&and;', '&or;', '&cap;', '&cup;', '&int;', '&there4;', '&sim;', '&cong;', '&asymp;', '&ne;', '&equiv;', '&le;', '&ge;', '&sub;', '&sup;', '&nsub;', '&sube;', '&supe;', '&oplus;', '&otimes;', '&perp;', '&sdot;', '&lceil;', '&rceil;', '&lfloor;', '&rfloor;', '&lang;', '&rang;', '&loz;', '&spades;', '&clubs;', '&hearts;', '&diams;');
        return this.swapArrayVals(s, arr1, arr2);
    },
    /* Numerically encodes all unicode characters */
    numEncode: function (s) {

        if (this.isEmpty(s))
            return "";

        var e = "";
        for (var i = 0; i < s.length; i++)
        {
            var c = s.charAt(i);
            if (c < " " || c > "~")
            {
                c = "&#" + c.charCodeAt() + ";";
            }
            e += c;
        }
        return e;
    },
    /* HTML Decode numerical and HTML entities back to original values */
    htmlDecode: function (s) {

        var c, m, d = s;

        if (this.isEmpty(d))
            return "";

        /* convert HTML entites back to numerical entites first */
        d = this.HTML2Numerical(d);

        /* look for numerical entities &#34; */
        arr = d.match(/&#[0-9]{1,5};/g);

        /* if no matches found in string then skip */
        if (arr != null) {
            for (var x = 0; x < arr.length; x++) {
                m = arr[x];
                c = m.substring(2, m.length - 1); /*get numeric part which is refernce to unicode character */
                /* if its a valid number we can decode */
                if (c >= -32768 && c <= 65535) {
                    /* decode every single match within string */
                    d = d.replace(m, String.fromCharCode(c));
                } else {
                    d = d.replace(m, ""); /*invalid so replace with nada */
                }
            }
        }

        return d;
    },
    /* encode an input string into either numerical or HTML entities */
    htmlEncode: function (s, dbl) {

        if (this.isEmpty(s))
            return "";

        /* do we allow double encoding? E.g will &amp; be turned into &amp;amp; */
        dbl = dbl | false; //default to prevent double encoding

        /* if allowing double encoding we do ampersands first */
        if (dbl) {
            if (this.EncodeType == "numerical") {
                s = s.replace(/&/g, "&#38;");
            } else {
                s = s.replace(/&/g, "&amp;");
            }
        }

        /* convert the xss chars to numerical entities ' " < > */
        s = this.XSSEncode(s, false);

        if (this.EncodeType == "numerical" || !dbl) {
            /* Now call function that will convert any HTML entities to numerical codes */
            s = this.HTML2Numerical(s);
        }

        /* Now encode all chars above 127 e.g unicode */
        s = this.numEncode(s);

        /* now we know anything that needs to be encoded has been converted to numerical entities we
         // can encode any ampersands & that are not part of encoded entities
         // to handle the fact that I need to do a negative check and handle multiple ampersands &&&
         // I am going to use a placeholder

         // if we don't want double encoded entities we ignore the & in existing entities */
        if (!dbl) {
            s = s.replace(/&#/g, "##AMPHASH##");

            if (this.EncodeType == "numerical") {
                s = s.replace(/&/g, "&#38;");
            } else {
                s = s.replace(/&/g, "&amp;");
            }

            s = s.replace(/##AMPHASH##/g, "&#");
        }

        /* replace any malformed entities */
        s = s.replace(/&#\d*([^\d;]|$)/g, "$1");

        if (!dbl) {
            /* safety check to correct any double encoded &amp; */
            s = this.correctEncoding(s);
        }

        /* now do we need to convert our numerical encoded string into entities */
        if (this.EncodeType == "entity") {
            s = this.NumericalToHTML(s);
        }

        return s;
    },
    /* Encodes the basic 4 characters used to malform HTML in XSS hacks */
    XSSEncode: function (s, en) {
        if (!this.isEmpty(s)) {
            en = en || true;
            /* do we convert to numerical or html entity? */
            if (en) {
                s = s.replace(/\'/g, "&#39;"); /* no HTML equivalent as &apos is not cross browser supported */
                s = s.replace(/\"/g, "&quot;");
                s = s.replace(/</g, "&lt;");
                s = s.replace(/>/g, "&gt;");
            } else {
                s = s.replace(/\'/g, "&#39;"); /* no HTML equivalent as &apos is not cross browser supported */
                s = s.replace(/\"/g, "&#34;");
                s = s.replace(/</g, "&#60;");
                s = s.replace(/>/g, "&#62;");
            }
            return s;
        } else {
            return "";
        }
    },
    /* returns true if a string contains html or numerical encoded entities */
    hasEncoded: function (s) {
        if (/&#[0-9]{1,5};/g.test(s)) {
            return true;
        } else if (/&[A-Z]{2,6};/gi.test(s)) {
            return true;
        } else {
            return false;
        }
    },
    /* will remove any unicode characters */
    stripUnicode: function (s) {
        return s.replace(/[^\x20-\x7E]/g, "");

    },
    /* corrects any double encoded &amp; entities e.g &amp;amp; */
    correctEncoding: function (s) {
        return s.replace(/(&amp;)(amp;)+/, "$1");
    },
    /* Function to loop through an array swaping each item with the value from another array e.g swap HTML entities with Numericals */
    swapArrayVals: function (s, arr1, arr2) {
        if (this.isEmpty(s))
            return "";
        var re;
        if (arr1 && arr2) {
            /*ShowDebug("in swapArrayVals arr1.length = " + arr1.length + " arr2.length = " + arr2.length)
             // array lengths must match */
            if (arr1.length == arr2.length) {
                for (var x = 0, i = arr1.length; x < i; x++) {
                    re = new RegExp(arr1[x], 'g');
                    s = s.replace(re, arr2[x]); /*swap arr1 item with matching item from arr2 */
                }
            }
        }
        return s;
    },
    inArray: function (item, arr) {
        for (var i = 0, x = arr.length; i < x; i++) {
            if (arr[i] === item) {
                return i;
            }
        }
        return -1;
    }

}

var awp_quantity = 0;
var awp_curr_prices = null;
var awp_center_images_done = new Array();
var awp_add_to_cart_button, awp_a2c_element, awp_a2c_original_text;  // last two to change button text

function awpComputePriceTotalPerChar(validComb, connectedPrice,  itr_impact)
{
            connectedGroupsChar = connectedAttributes[validComb]['id_attribute_groups'];
        totalPricePerCharGroup = new Array();
        impactPerChar = false;
        totalPricePerChar = 0;
        for (i in connectedGroupsChar) {
            idGroupChar = parseInt(connectedGroupsChar[i]);
            totalPricePerCharGroup[idGroupChar] = 0;
            if (typeof awp_groups_chars[idGroupChar] != 'undefined' && awp_groups_chars[idGroupChar]['price_impact_per_char'] == 1) {
                minLimitCharge = awp_groups_chars[idGroupChar]['group_min_limit'];
                
                 
                if (typeof minLimitCharge == 'undefined' || minLimitCharge == 0 || minLimitCharge < 0) {
                    $minLimitCharge = 1;
                }
                exceptions = awp_groups_chars[idGroupChar]['exceptions'];
                connectedAttributesChar = connectedAttributes[validComb]['attributes_to_groups'][idGroupChar];
                for (k in connectedAttributesChar) {
                   groupType = awp_group_type[idGroupChar];
                    if (groupType == 'textarea')
                       valChars = $('#awp_textarea_group_' + connectedAttributesChar[k]).val();
                    else
                        valChars = $('#awp_textbox_group_' + connectedAttributesChar[k]).val();
                    if (valChars != '') {                        
                        if (exceptions != '') {
                            exceptionsArr = exceptions.split('');
                            for (i in exceptionsArr) {
                                valChars = valChars.split(exceptionsArr[i]).join('');
                            }
                        }

                        charsValCount = valChars.length;
                        priceImpactPerChar = 0;
                        charsNo = 0;
                        if (minLimitCharge < charsValCount) {
                            charsNo = charsValCount;
                            priceImpactPerChar = charsValCount * parseFloat(connectedPrice);
                        } else {
                            charsValCount = parseFloat(minLimitCharge);
                            priceImpactPerChar = parseFloat(minLimitCharge) * parseFloat(connectedPrice);
                        }
                        totalPricePerCharGroup[idGroupChar] += priceImpactPerChar;
                        totalPricePerChar += priceImpactPerChar;     
                        impactPerChar = true;
                        
                    }
                }

            } 
        }
        if (impactPerChar) {
            totalPriceChar = 0;
            for (k in totalPricePerCharGroup) {
                if (typeof totalPricePerCharGroup[k] != undefined )
                    totalPriceChar += parseFloat(totalPricePerCharGroup[k]);
            }
            
            awp_new_price = parseFloat(itr_impact) + parseFloat(totalPriceChar);
        } else {
            awp_new_price = parseFloat(itr_impact) + parseFloat(connectedPrice);
        }
        return awp_new_price;
}

function awp_select(group, attribute, currency, first)
{
    /*if (first)
     //return false;
     alert("awp_select("+group+","+attribute+","+first);*/
    if (typeof attribute == 'undefined')
        return;
    if (awp_group_type[group] == "file" && attribute != parseInt(attribute))
    {
        var awp_file_arr = attribute.split('_');
        attribute = awp_file_arr[1];
    }
    /*alert(group + " - "+ attribute + " = "+awp_selected_groups.toString())
     //if (awp_no_tax_impact)
     // displayPrice = 1;
     // Get Current price based on all selected attributes //
     First time the page loads, only get the prices one time */
    if (awp_curr_prices == null || !first)
    {
        awp_curr_prices = awp_price_update();
    }

    var curr_price = awp_curr_prices['priceProduct'];

     // Change product image based on attribute, and change "Customize" to add to cart// */
    if (!first)
    {
       if (awp_a2c_original_text !== awp_a2c_element.text()) {
            // replace Customize text with Add to Cart (theme original text)
            awp_a2c_element.html(awp_a2c_element.html().replace(awp_customize, awp_a2c_original_text));
        }

        awp_add_to_cart_button.unbind('click').click(function () {
            awp_add_to_cart_button.prop('disabled', true);
            awp_add_to_cart();
            awp_add_to_cart_button.prop('disabled', false);
            return false;
        });
    }
    /* alert(group+", "+attribute+", "+currency+", "+first); */
    var name = document.awp_wizard;
    var default_impact = parseFloat($("#pi_default_" + group).val());
    var current_impact = parseFloat(typeof awp_impact_list[attribute] != 'undefined' ? awp_impact_list[attribute] : 0);
    /* alert("1) " + awp_selected_groups[group]); */
    
    
    while (awp_array_key(0, awp_selected_groups) > 0) {
        delete awp_selected_groups[awp_array_key(0, awp_selected_groups)];
    }
    
    if ($('#awp_checkbox_group_' + attribute).length) {
        if ($('#awp_checkbox_group_' + attribute).is(':checked')) {
            awp_selected_groups[group] = attribute;
        } else {
            if (awp_in_array(attribute, awp_selected_groups) != -1) {
                delete awp_selected_groups[awp_array_key(attribute, awp_selected_groups)];
            }
        }
    } else {
        awp_selected_groups[group] = attribute;
    }

    $("#pi_default_" + group).val(current_impact);
    $(".awp_group_class_" + group).each(function () {
        if ($(this).attr('checked') && $(this).val() != attribute && typeof awp_impact_list[$(this).val()] != 'undefined')
            current_impact += parseFloat(awp_impact_list[$(this).val()]);
    });
    if (awp_pi_display != "")
    {
        for (var id_attribute in awp_impact_list)
        {
            var id_group = awp_attr_to_group[id_attribute];
            var group_type = awp_group_type[id_group];
            /* No price impact for the group, can skip // */
            if (typeof awp_selected_groups[id_group] == 'undefined' || (awp_pi_display == "total" && awp_group_impact[id_group] != 1))//parseFloat(awp_impact_list[id_attribute]) == 0))
            {
                /* alert(id_group+" --------> "+id_attribute); */
                continue;
            }
            var tmp_attr = 0;
            /* alert(awp_selected_groups[id_group]); */
            if (awp_pi_display == "total" || (group_type != "checkbox" && group_type != "textbox" && group_type != "textarea" && group_type != "file" && awp_selected_groups[id_group] != 0))
            {
                /* alert("tmp_attr = " + awp_selected_groups[id_group] + " - "+id_group +" ---> " +group_type); */
                tmp_attr = awp_selected_groups[id_group];
            } else
            {
                /* alert("tmp_attr2 = " + id_attribute + " - "+id_group); */
                tmp_attr = id_attribute;
            }
            /* alert("tmp_attr = " + tmp_attr + awp_impact_list.toSource()); */
            //console.log("tmp_attr = " + tmp_attr);
            current_impact = parseFloat(awp_impact_list[tmp_attr]);
            //console.log("current_impact = " + current_impact);
            /* if (group_type == "file" && typeof awp_impact_list[tmp_attr] == 'undefined')
             alert(current_impact + " == " + tmp_attr + " == " + id_attribute + " == " + awp_selected_groups[id_group]); */
            if (group_type == "calculation")
                current_impact = parseFloat(awp_impact_list[id_attribute]) * awp_selected_groups[id_group] / 1000000;
            /* alert(id_attribute + ") " + tmp_attr + " - " + current_impact)
             //alert(is_checkbox + " | " +id_group + " --->>> " + id_attribute + " = " + tmp_attr + " == " +current_impact);

             // Only change the price impact for the current group
             // except when running for the first time, of when price impact is set to total
             //alert(awp_group_impact[id_group] + " == 1 && " + id_group + "  ==  "+group); */
            if ((awp_group_impact[id_group] == 1 && id_group == group && (awp_pi_display != "total" || first))
                ||
                (awp_pi_display == "total" && (!first || awp_group_impact[id_group] == 1)))
            {
                var selected = document.getElementById("awp_group_" + id_group) ? document.getElementById("awp_group_" + id_group).selectedIndex : 0;
                var select_group = false;
                /* if (!first)
                 //alert(group +" - "+ id_attribute); */
                var html = " ";
                /* if ($("#awp_group_per_row_"+id_group).val() > 1 && $("#awp_group_layout_"+id_group).val() == 1 && awp_hin[id_group] != 1)
                 //html = "<br />";
                 // Displaying price difference */
                if (awp_pi_display == "diff")
                {
                    var itr_impact = awp_impact_list[id_attribute];
                    /*if (group_type == "file")
                     //alert(id_attribute+" === "+awp_impact_list[id_attribute] +" == " + current_impact);
                     alert(id_attribute + " - " + tmp_attr + " - " + current_impact + " <> " +itr_impact); */
                    var awp_new_price = 0;
                    /* Current impact smaller, Show Add */


                    if (current_impact < itr_impact)
                    {
                        awp_new_price = (Math.ceil(Math.abs(current_impact - itr_impact)) == Math.abs(current_impact - itr_impact) ? Math.abs(current_impact - itr_impact) : Math.abs(current_impact - itr_impact));
                         if (typeof displayPrice != 'undefined' && displayPrice == 0 && (awp_no_tax_impact ))
                            awp_new_price *= 1 + (taxRate / 100);
                        awp_new_price *= ((100 - reduction_percent) / 100);
                        /* alert(awp_new_price + " = " + currencyRate); */
                        html += "[" + awp_add + " " + awpFormatCurrency(awp_new_price * awp_group_reduction * currencyRate, currencyFormat, currencySign, currencyBlank) + "]";
                    }
                    /* Current impact larger, Show Subtract  */
                    else if (current_impact > itr_impact)
                    {
                        awp_new_price = (Math.ceil(Math.abs(itr_impact - current_impact)) == Math.abs(itr_impact - current_impact) ? Math.abs(itr_impact - current_impact) : Math.abs(itr_impact - current_impact));
                        if (typeof displayPrice != 'undefined' && displayPrice == 0 && (awp_no_tax_impact ))
                            awp_new_price *= 1 + (taxRate / 100);
                        awp_new_price *= ((100 - reduction_percent) / 100);
                        /*alert(awp_new_price + " == " + currencyRate);*/
			if(awp_new_price) {
                        html += "[" + awp_sub + " " + awpFormatCurrency(awp_new_price * awp_group_reduction * currencyRate, currencyFormat, currencySign, currencyBlank) + "]";
			}
                        //alert(current_impact+" > "+itr_impact+" == "+html);
                    }
                    /* Impact is the same, update price with tax + currency  */
                    else if (current_impact != 0 && first && (current_impact != itr_impact || group_type == "checkbox" || group_type == "textarea" || group_type == "textbox" || group_type == "file"))// && id_attribute != attribute)
                    {
                        awp_new_price = Math.abs(current_impact);
                       if (typeof displayPrice != 'undefined' && displayPrice == 0 && (awp_no_tax_impact ))
                            awp_new_price *= 1 + (taxRate / 100);
                        awp_new_price *= ((100 - reduction_percent) / 100);
                        /*alert(awp_new_price + " === " + currencyRate);*/
                        if (current_impact < 0)
                            html += "[" + awp_sub + " " + awpFormatCurrency(awp_new_price * awp_group_reduction * currencyRate, currencyFormat, currencySign, currencyBlank) + "]";
                        else if (current_impact > 0)
                            html += "[" + awp_add + " " + awpFormatCurrency(awp_new_price * awp_group_reduction * currencyRate, currencyFormat, currencySign, currencyBlank) + "]";
                        //alert(current_impact+" != "+itr_impact+" == "+html);
                        /* alert(html); */
                    } else
                    {
                        /* alert(id_attribute+" = "+current_impact); */
                    }
                    /* alert(current_impact + " - " + itr_impact + " = " + html); */

                }
                /* Displaying Total price */
                else
                {

                    /* Connected Attributes */
                    /* Disable add impact for connected attributes  */
                    var connectedAttrs = new Array();

                    for (combinationA in connectedAttributes) {
                        for (connectedAttrVals in connectedAttributes[combinationA]['attributes']) {
                            connectedAttrs.push(connectedAttributes[combinationA]['attributes'][connectedAttrVals]);
                        }
                    }


                    var connectedGroups = new Array();

                    for (combinationA in connectedAttributes) {
                        for (connectedGroup in connectedAttributes[combinationA]['attributes_to_groups']) {
                            connectedGroups.push(connectedGroup);
                        }
                    }
                    connected = false;

                    new_choice = awp_selected_groups_multiple;

                    /* Get the selected combination */
                    var validCombArray = new Array();
                    var validGroupArray = new Array();
                    for (groupId in awp_selected_groups_multiple) {
                        for (idAttr in awp_selected_groups_multiple[groupId]) {
                            if (awp_in_array(groupId, connectedGroups)) {
                                if (id_group != groupId) {

                                    validCombArray.push(awp_selected_groups_multiple[groupId][idAttr]);
                                } else {

                                    validCombArray.push(id_attribute);
                                }
                            }
                            validGroupArray.push(groupId);
                        }
                    }

                    /* Check if the selected combination is in the array of connected combinations */
                    for (combinationA in connectedAttributes) {
                        if (arrayContainsAnotherArray(validCombArray, connectedAttributes[combinationA]['attributes'])) {
                            connected = true;
                            validComb = combinationA;
                        }
                    }

                    /* If combination is connected and attribute is in the array of connected attributes */
                    if (connected && awp_in_array(id_attribute, connectedAttrs)) {
                        var itr_impact = parseFloat(0) * currencyRate;
                        current_impact = 0;

                    } else {
                        var itr_impact = parseFloat(awp_impact_list[id_attribute]) * currencyRate;

                    }

                    /* End - Connected Attributes */

                    var att_selected = false;
                    if (awp_selected_groups[id_group] == id_attribute)
                        att_selected = true;
                    /* if (!first)
                     //alert(id_group+" --------> "+id_attribute+")  --- "+current_impact +" - "+ itr_impact + " == " +att_selected);
                     //if ((group_type == "checkbox" && !$("#awp_checkbox_group_"+id_attribute).attr('checked')) || (true))
                     //{ */
                    if (itr_impact != 0 || awp_group_impact[id_group] == 1)
                    {
                        /* alert("awp_get_total_prices ("+id_group+","+id_attribute+") == "+curr_price+" === " + current_impact+ " - " + itr_impact); */

                        var tmp_arr = new Array();
                        var awp_new_price = 0;
                        /* The following group types allow multiple selection, and require special calculation */
                        if (group_type == "checkbox" || group_type == "file" || group_type == "textbox" || group_type == "textarea")
                        {
                            /* alert(curr_price + ") " + itr_impact + " + " +current_impact + " ("+id_attribute+" , "+attribute); */
                            if (group_type == "checkbox" && !$("#awp_checkbox_group_" + id_attribute).attr('checked'))
                                awp_new_price = itr_impact;
                            else if (group_type == "textbox" && !$("#awp_textbox_group_" + id_attribute).val() != '')
                                awp_new_price = itr_impact;
                            else if (group_type == "file" && !$("#awp_file_group_" + id_attribute).val() != '')
                                awp_new_price = itr_impact;
                            else if (group_type == "textarea" && !$("#awp_textarea_group_" + id_attribute).val() != '')
                                awp_new_price = itr_impact;
                            /* alert("1.1) "+awp_new_price); */
                        } else if (group_type == "quantity")
                            awp_new_price = itr_impact;
                        else if (current_impact < itr_impact)
                        {
                            awp_new_price = (itr_impact - current_impact);
                            //console.log("1.2) "+awp_new_price);
                            /* alert("1.2) "+awp_new_price); */
                        } else if (current_impact > itr_impact)
                        {
                            awp_new_price = (0 - current_impact + itr_impact);
                            /* alert("1.3) "+awp_new_price); */
                        }
                        if (typeof displayPrice != 'undefined' && displayPrice != 1 && awp_no_tax_impact)
                        {
                            awp_new_price *= 1 + (taxRate / 100);
                            /* alert("1.5) "+displayPrice+" =  "+awp_new_price + " / " + taxRate); */
                        }
                        //console.log('awp_new_price = ' +awp_new_price + ' curr_price ' + curr_price);
                        awp_new_pricexxx = awp_new_price;
                        awp_new_price *= ((100 - reduction_percent) / 100);
                        awp_new_price *= awp_group_reduction;
                        /* alert("2) "11+awp_new_price + " - " + curr_price); */
                        // console.log('awp_new_price = ' +awp_new_price + ' curr_price ' + curr_price);
                        awp_new_price += curr_price;
                        awp_new_price = Math.max(awp_new_price, 0);
                        //console.log('id_attribute= '+id_attribute+'reduction_percent = ' +reduction_percent + 'awp_new_price = ' +awp_new_price + 'itr_impact = ' +itr_impact );

                        html += "[" + awpFormatCurrency(awp_new_price, currencyFormat, currencySign, currencyBlank) + "]";
                    }
                }
                /* alert($("#price_change_"+id_attribute).length + " = " + id_attribute + " ->"+html); */
                if ($("#price_change_" + id_attribute).length != 0)
                {
                    /*if (!first)
                     //alert(id_group+" -> "+id_attribute+") "+current_impact +" ("+$("#awp_radio_group_"+id_attribute).attr('checked')+") - "+ itr_impact + " == " +html);
                     //alert(id_attribute+ " - " + html); */
                    if (first || awp_pi_display == "total" || (group_type != "checkbox" && group_type != "file" && group_type != "textbox" && group_type != "textarea"))
                        $("#price_change_" + id_attribute).html(html.replace(" ", "&nbsp;"));

                    //console.log(id_group+" -> "+id_attribute+") "+current_impact +" ("+$("#awp_radio_group_"+id_attribute).attr('checked')+") - "+ itr_impact + " == " +html);
                } else if (document.getElementById("awp_group_" + id_group) && document.getElementById("awp_group_" + id_group).options)
                {
                    var currentSB = document.getElementById("awp_group_" + id_group);
                    var sb_index = 0;
                    var tmp_index = 0;
                    var cur_class = "";
                    /*
                    * $("#awp_group_" + id_group + " option") had length = *2 of real length, which caused wrong sb_index
                     */
                    $("#awp_group_" + id_group).find("option").each(function () {
                        if (id_attribute == $(this).val())
                        {
                            sb_index = tmp_index;
                            cur_class = $(this).attr('class');
                        }
                        tmp_index++;
                    });
                    var current = document.getElementById("awp_group_" + id_group).options[sb_index].text;
                    /* if (!first)
                     //	alert(id_group+" -> "+id_attribute+" ["+sb_index+"]>> "+current_impact +" - "+ itr_impact + " ("+current+") == " +html);
                     //alert(id_attribute + " = " + sb_index + " = " + current + " | "+ html); */
                    if (current.lastIndexOf("[") > 0)
                        current = current.substring(0, current.lastIndexOf("["));
                    /* document.getElementById("awp_group_"+id_group).options[sb_index]=new Option(current+html, id_attribute) */
                    $("#awp_group_" + id_group + " option:eq(" + sb_index + ")").text(current + html);
                    document.getElementById("awp_group_" + id_group).selectedIndex = selected;
                    /* alert("Select box " + split[4] + " -- " + current+html); */
                    select_group = true;
                    //console.log(id_group+" -> "+id_attribute+" ["+sb_index+"]>> "+current_impact +" - "+ itr_impact + " ("+current+") == " +html);
                }
            }
        }



    }

    /*Connected attributes */
    if (connectedAttributes.length > 0) {

        /* Connected attributes */
        var awp_valid_arr = new Array();
        var connectedGroups = new Array();

        for (combinationA in connectedAttributes) {
            for (connectedGroup in connectedAttributes[combinationA]['attributes_to_groups']) {
                connectedGroups.push(connectedGroup);
            }
        }
        found = false;
        /* Get the latest valid combination available for the selected id_attribute */
        for (combinationA in connectedAttributes) {
            for (connectedGroup in connectedAttributes[combinationA]['attributes_to_groups']) {
                connectedGroups.push(connectedGroup);
                if (group == connectedGroup) {
                    for (connectedValue in connectedAttributes[combinationA]['attributes_to_groups'][connectedGroup]) {
                        if (attribute == connectedAttributes[combinationA]['attributes_to_groups'][connectedGroup][connectedValue]) {
                            found = true;
                            awp_valid_arr = connectedAttributes[combinationA]['attributes_to_groups'];
                            break;
                        }
                    }
                }
            }
        }
        var choice = new Array();
        choice = awp_selected_groups;

        /* Get the current attribute selection & try to identify a connected attribute */
        new_choice = awp_selected_groups_multiple;

        var max = 0;
        for (combinationA in connectedAttributes) {
            for (connectedGroup in connectedAttributes[combinationA]['attributes']) {
                /* Counts the duplicate values from selected options and all connected attributes */
                countContains = countArrayInArray(new_choice, connectedAttributes[combinationA]['attributes']);

                /* Select the highest match of selection options and connected attribute */
                if (countContains >= max && awp_in_array(attribute, connectedAttributes[combinationA]['attributes'])) {
                    /* If the selected valid combination is available (in stock or allows to buy when out of stock - select it*/
                    if ((!allowBuyWhenOutOfStock && connectedAttributes[combinationA]['quantity'] > 0) || allowBuyWhenOutOfStock) {
                        max = countContains;
                        awp_valid_arr = connectedAttributes[combinationA]['attributes_to_groups'];
                    }
                }
            }
        }
        //console.log(connectedAttributes);
        //console.log('found = ' + found );
        //console.log('new_choice');
        //console.log(new_choice);
        //console.log('max');
        //console.log(max);
        //console.log('awp_valid_arr');
        //console.log(awp_valid_arr);
        //console.log('choice');
        //console.log(choice);

        /* Check if all selected attributes (if multiselect attribute group) is included in the selected valid combination */
        var validSelected = true;

        for (connectedGroup in awp_valid_arr) {
            attributeValues = awp_valid_arr[connectedGroup];
            if (!arrayContainsAnotherArray(new_choice[connectedGroup], attributeValues))
                validSelected = false;
        }


        /* If selected options are not valid , select a valid combination */
        /* containsSearchAll - ignore if product contain the same attributes for both connected and normal attributes - affects only price and stock */
        if (!containsSearchAll && !validSelected) {
            /* Parse the valid selected connected combination*/
            for (connectedGroup in awp_valid_arr) {
                attributeValues = awp_valid_arr[connectedGroup];
                /* Dropdown */
                $('select#awp_group_' + connectedGroup).val(attributeValues[0]);

                $('select#awp_group_' + connectedGroup + ' option').each(function () {
                    $(this).removeAttr('selected');
                });
                $('select[name="awp_group_' + connectedGroup + '"]' + ' option[value=' + attributeValues[0] + ']').attr('selected', 'selected');

                $('select#awp_group_' + connectedGroup).val(attributeValues[0]);

                if (awp_out_of_stock == 'hide') {

                    if (isIE && $('select[name="awp_group_' + connectedGroup + '"]' + ' option[value=' + attributeValues[0] + ']').parent().is('span'))
                        $('select[name="awp_group_' + connectedGroup + '"]' + ' option[value=' + attributeValues[0] + ']').unwrap().show();
                    else
                        $('select[name="awp_group_' + connectedGroup + '"]' + '[value=' + attributeValues[0] + ']').show();
                } else if (awp_out_of_stock == 'disable') {
                    $('select[name="awp_group_' + connectedGroup + '"]' + ' option[value=' + attributeValues[0] + ']').removeAttr('disabled');

                }
                /* End - Dropdown */

                /* Radio & Single Select */
                $("input:radio[name='awp_group_" + connectedGroup + "']").each(function () {
                    $(this).prop('checked', false);
                    $(this).removeAttr('checked');
                });
                $("input:radio[name='awp_group_" + connectedGroup + "'][value=" + attributeValues[0] + "]").prop('checked', 'checked');
                $("input:radio[name='awp_group_" + connectedGroup + "'][value=" + attributeValues[0] + "]").attr('checked', 'checked');
                if (awp_out_of_stock == 'hide') {
                    $('#awp_cell_cont_' + attributeValues[0]).css('display', 'block');

                } else if (awp_out_of_stock == 'disable') {
                    $('#awp_radio_group_' + attributeValues[0]).removeAttr('disabled');
                    $('#awp_cell_' + attributeValues[0]).enable();


                }
                /* END - Radio & Single Select */

                /* Checkbox */
                $("input:checkbox[name='awp_group_" + connectedGroup + "']").each(function () {
                    $(this).prop('checked', false);
                    $(this).removeAttr('checked');
                });

                $("input:checkbox[name='awp_group_" + connectedGroup + "'][value=" + attributeValues[0] + "]").prop('checked', 'checked');

                if (awp_out_of_stock == 'hide') {
                    $('#awp_cell_cont_' + attributeValues[0]).css('display', 'block');
                } else if (awp_out_of_stock == 'disable') {

                    $('#awp_radio_group_' + attributeValues[0]).removeAttr('disabled');

                    $('#awp_checkbox_cell' + attributeValues[0]).removeAttr('disabled');
                    $('#awp_checkbox_group_' + attributeValues[0]).removeAttr('disabled');

                    $('#awp_cell_' + attributeValues[0]).enable();

                }
                /* END - Checkbox */


                /* Textbox */
                $("input:text[class='awp_attribute_selected awp_group_class_" + connectedGroup + "']").each(function () {
                    $(this).val('');
                });

                $("input:text[name='awp_group_" + connectedGroup + "_" + attributeValues[0] + "']").val('');

                if (awp_out_of_stock == 'hide') {
                    $('#awp_cell_cont_' + attributeValues[0]).css('display', 'block');
                } else if (awp_out_of_stock == 'disable') {
                    $("input:text[name='awp_group_" + connectedGroup + "_" + attributeValues[0] + "']").removeAttr('disabled');

                    $("input:text[name='awp_group_" + connectedGroup + "_" + attributeValues[0] + "']").enable();

                }
                /* END - Textbox */


                /* Textarea */
                $("textarea[class='awp_attribute_selected awp_group_class_" + connectedGroup + "']").each(function () {
                    $(this).val('');
                });

                $("textarea[name='awp_group_" + connectedGroup + "_" + attributeValues[0] + "']").val('');

                if (awp_out_of_stock == 'hide') {
                    $('#awp_cell_cont_' + attributeValues[0]).css('display', 'block');
                } else if (awp_out_of_stock == 'disable') {
                    $("textarea[name='awp_group_" + connectedGroup + "_" + attributeValues[0] + "']").removeAttr('disabled');

                    $("textarea[name='awp_group_" + connectedGroup + "_" + attributeValues[0] + "']").enable();

                }
                /* END - Textarea */

                /* FileUpload */
                $("input:hidden[class='awp_attribute_selected awp_group_class_" + connectedGroup + "']").each(function () {
                    $(this).val('');
                });

                $("input:hidden[name='awp_group_" + connectedGroup + "_" + attributeValues[0] + "']").val('');

                if (awp_out_of_stock == 'hide') {
                    $('#awp_cell_cont_' + attributeValues[0]).css('display', 'block');
                } else if (awp_out_of_stock == 'disable') {
                    $('#upload_button_' + attributeValues[0]).removeAttr('disabled');
                    $('#upload_button_' + attributeValues[0]).enable();

                }
                applyFileUpload();
                /* END - FileUpload */


                /* Quantity */
                $("input:text[alt='awp_group_" + connectedGroup + "']").each(function () {
                    $(this).val('0');
                });

                $("input:text[alt='awp_group_" + connectedGroup + "'][name='awp_group_" + connectedGroup + "_" + attributeValues[0] + "']").val('1');

                if (awp_out_of_stock == 'hide') {
                    $('#awp_cell_cont_' + attributeValues[0]).css('display', 'block');
                } else if (awp_out_of_stock == 'disable') {
                    $("input:text[name='awp_group_" + connectedGroup + "_" + attributeValues[0] + "']").removeAttr('disabled');

                    $("input:text[name='awp_group_" + connectedGroup + "_" + attributeValues[0] + "']").enable();

                }
                /* END - Quantity */

            }

            /* Compute the prices based on the new valid connected combination */
            var prices = awp_get_total_prices(0,  0);
            var choice = new Array();
            choice = awp_selected_groups;
            /* Update prices based on the new valid connected combination */
            awp_price_update();
            /* Reload all uniform elements*/
            try
            {
                $.uniform.update();
            } catch (err)
            {
            }
        }



        /* Price Diff - remove it totally for connected attributes */
        /* Price total - display the total price next to each attribute */
        if (awp_pi_display != '') {
            /* Connected Attributes */
            var connectedGroups = new Array();

            for (combinationA in connectedAttributes) {
                for (connectedGroup in connectedAttributes[combinationA]['attributes_to_groups']) {
                    connectedGroups.push(connectedGroup);
                }
            }
            var allGroups = new Array();
            for (idGroup in awp_selected_groups) {
                allGroups.push(idGroup);
            }
            
            for (idGroupK in connectedGroups) {
                idGroup = connectedGroups[idGroupK];
                $('.awp_cell_cont_' + idGroup).each(function () {
                    var id_attr = $(this).attr('id');

                    id_attr = id_attr.replace("awp_cell_cont_", "");
                    if (awp_pi_display == "diff")
                    {
                        /* If price display is difference - remove the price diff from connected attributes*/
                        html = ' ';
                        $("#price_change_" + id_attr).html(html.replace(" ", "&nbsp;"));
                        
                        if (document.getElementById("awp_group_" + idGroup) && document.getElementById("awp_group_" + idGroup).options)
                        {
                            var currentSB = document.getElementById("awp_group_" + idGroup);
                            var sb_index = 0;
                            var tmp_index = 0;
                            var cur_class = "";
                            $("#awp_group_" + idGroup + " option").each(function () {
                                var current = document.getElementById("awp_group_" + idGroup).options[sb_index].text;
                                if (current.lastIndexOf("[") > 0)
                                current = current.substring(0, current.lastIndexOf("["));
                                document.getElementById("awp_group_" + idGroup).options[sb_index].text = current;
								sb_index++;
                            });
                            



                        }
                    }
                });
            }


        }

        /* Compute total price next to each attribute */
        if (awp_pi_display == "total") {
            new_choice = awp_selected_groups_multiple;

            var connectedGroups = new Array();
            var allConnectedAttributes = new Array();

            for (combinationA in connectedAttributes) {
                for (connectedGroup in connectedAttributes[combinationA]['attributes_to_groups']) {
                    connectedGroups.push(connectedGroup);
                }
            }
            connectedGroups = toUnique(connectedGroups);

            for (combinationA in connectedAttributes) {
                for (idAttr in connectedAttributes[combinationA]['attributes']) {
                    allConnectedAttributes.push(connectedAttributes[combinationA]['attributes'][idAttr]);
                }
            }

            /* Compute group id selection */
            var validCombArray = new Array();
            var validGroupArray = new Array();
            for (groupId in awp_selected_groups_multiple) {
                for (idAttr in awp_selected_groups_multiple[groupId]) {
                    validCombArray.push(awp_selected_groups_multiple[groupId][idAttr]);
                    validGroupArray.push(groupId);
                }
            }

            var groupNotConnectedImpact = new Array();


            /* For all selected groups
             *	Compute total Price with the last selected attribute value and all other attribute groups
             *	Eg.: Attribute value = 1, Attribute Group = 1 => Computed with all attribute values from Group 1,2....
             */
            for (groupId in awp_selected_groups_multiple) {
                /* Parse all attributes */
                $("input[name='awp_group_" + groupId + "'], select[name='awp_group_" + groupId + "'] option, input:text[class='awp_attribute_selected awp_group_class_" + groupId + "'], textarea[class='awp_attribute_selected awp_group_class_" + groupId + "'], input:text[alt='awp_group_" + groupId + "'], input:hidden[class='awp_attribute_selected awp_group_class_" + groupId + "']").each(function () {

                    id = $(this).attr('id');
                    /* Get id_attribute = group_option*/
                    if ($(this).attr('id') && id.indexOf('awp_textbox_group_') > -1) {
                        var group_option = $(this).attr('id');
                        group_option = group_option.replace("awp_textbox_group_", "");
                    } else if ($(this).attr('id') && id.indexOf('awp_textarea_group_') > -1) {
                        var group_option = $(this).attr('id');
                        group_option = group_option.replace("awp_textarea_group_", "");
                    } else if ($(this).attr('id') && id.indexOf('awp_quantity_group_') > -1) {
                        var group_option = $(this).attr('id');
                        group_option = group_option.replace("awp_quantity_group_", "");
                    } else if ($(this).attr('id') && id.indexOf('awp_file_group_') > -1) {
                        var group_option = $(this).attr('id');
                        group_option = group_option.replace("awp_file_group_", "");
                    } else {
                        var group_option = $(this).val();
                    }

                    /* Compute temporary choice for connected attribute and for not connected attributes */
                    var tmp_choice = new Array();
                    var tmp_choice_notConn = new Array();
                    var pos = 0;
                    var group_parent = $(this).attr('name');

                    for (var key in choice) {

                        // temp fix  todo check choice arr filling
                        if(!isValid(choice[key])) {
                            return;
                        }

                        if (key == groupId) {
                            if (connectedGroups.indexOf(key) >= 0) {
                                tmp_choice[key] = new Array();
                                tmp_choice[key].push(group_option);
                            } else {
                                tmp_choice_notConn[key] = new Array();
                                tmp_choice_notConn[key].push(group_option);
                            }
                        } else {
                            if (connectedGroups.indexOf(key) >= 0) {
                                tmp_choice[key] = new Array();
                                tmp_choice[key].push(choice[key].toString());
                            } else {
                                tmp_choice_notConn[key] = new Array();
                                tmp_choice_notConn[key].push(choice[key].toString());
                            }
                        }
                    }

                    var tmp_choice2 = new Array();
                    for (key in tmp_choice) {
                        for (key2 in tmp_choice[key]) {
                            tmp_choice2.push(tmp_choice[key][key2].toString());
                        }
                    }

                    tmp_choice = tmp_choice2;

                    /* Check if the temporary connected attribute is valid */
                    var valid = false;
                    var validComb = 0;
                    for (combinationA in connectedAttributes) {
                        if (arrayContainsAnotherArray(tmp_choice, connectedAttributes[combinationA]['attributes'])) {
                            valid = true;
                            validComb = combinationA;

                        }
                    }

                    /* If temp connected attribute is valid compute price*/
                    if (valid) {

                        /* validCombArray - contains both connected and not connected attributes */
                        var validCombArray = new Array();


                        for (idAttr in tmp_choice_notConn) {
                            validCombArray.push(tmp_choice_notConn[idAttr]);

                        }

                        for (idAttr in tmp_choice) {
                            validCombArray.push(tmp_choice[idAttr]);

                        }
                        //	console.log('AFFECTS');
                        //console.log('tmp_choice');
                        //console.log(tmp_choice);
                        //console.log('validCombArray');
                        //console.log(validCombArray);
                        //console.log('validGroupArray');
                        //console.log(validGroupArray);

                        //console.log('connectedGroups');
                        //console.log(connectedGroups);


                        /* Compute the array which stores the price impact for not connected attributes */
                        if (arrayContainsAnotherArray(tmp_choice, validCombArray)) {
                            /* Compute Array Difference */
                            var diff = $(validGroupArray).not(connectedGroups).get();

                            for (k in diff) {
                                groupNotConnectedImpact[diff[k]] = connectedAttributes[validComb]['price'];

                            }
                        }
                        /* Array diff to get total impact for selected combinations which are not connected */
                        var attributesNotConnected = $(validCombArray).not(allConnectedAttributes).get();
                        //console.log('attributesNotConnected');
                        //console.log(attributesNotConnected);

                        //console.log('allConnectedAttributes');
                        //console.log(allConnectedAttributes);
                        /* Add impact from all temp (selected) not connected attribute*/
                        var itr_impact = 0;

                        for (k in attributesNotConnected) {
                            if (awp_in_array(attributesNotConnected[k], validCombArray)) {
                                if (typeof awp_impact_list[attributesNotConnected[k]] != 'undefined') {
                                    itr_impact += parseFloat(parseFloat(awp_impact_list[attributesNotConnected[k]]) * currencyRate);


                                }
                            }
                        }

                        //console.log('itr_impact NOT CONNECTED IMPACT ONLY');
                        //console.log(itr_impact);
                        
                        productPrice = productBasePriceTaxIncl;
                        
                        

                        /* Regenerate product price - by default it contains also the default combination price.
                         If the default combination is connected and has a price impact we need to remove it*/

                        defaultConnectedAttributeWithTax = defaultConnectedAttribute['price'];
                        if (typeof displayPrice != 'undefined' && displayPrice != 1 && awp_no_tax_impact)
                        {
                            defaultConnectedAttributeWithTax = defaultConnectedAttribute['price'] * (1 + (taxRate / 100));
                            
                            productPrice = productPrice  * (1 + (taxRate / 100));
                            /* alert("1.5) "+displayPrice+" =  "+awp_new_price + " / " + taxRate); */
                        }


                        productPriceNew = parseFloat(productPrice);
                        //console.log('group_option');
                        //console.log(group_option);

                        if (typeof displayPrice != 'undefined' && displayPrice != 1 && awp_no_tax_impact)
                        {
                            itr_impact *= 1 + (taxRate / 100);
                            /* alert("1.5) "+displayPrice+" =  "+awp_new_price + " / " + taxRate); */
                        }



                        itr_impact += parseFloat(productPriceNew);



                        if ($("#price_change_" + group_option).length != 0)
                        {
                            //priceChange = $("#price_change_"+group_option).html();
                            //priceChange = priceChange.replace("[", "");
                            //priceChange = priceChange.replace("]", "");
                            /* If the temp attribute is in bothConnectedAttributes then update the price*/
                            if (awp_in_array(group_option, bothConnectedAttributes)) {
                                connectedPrice = connectedAttributes[validComb]['price'];
                                if (typeof displayPrice != 'undefined' && displayPrice != 1 && awp_no_tax_impact)
                                {
                                    connectedPrice *= 1 + (taxRate / 100);
                                    /* alert("1.5) "+displayPrice+" =  "+awp_new_price + " / " + taxRate); */
                                }
                                connectedPrice *= (100-reduction_percent ) / 100;

                                /* Add the temp connected attribute impact to the total price */
                               
                               awp_new_price =  awpComputePriceTotalPerChar(validComb, connectedPrice,  itr_impact);     
       

                                //console.log("INPUT groupId = " +groupId + " group_option = " + group_option + " itr_impact = " + itr_impact + " + price = " + connectedAttributes[validComb]['price']);

                                html = "[" + awpFormatCurrency(awp_new_price, currencyFormat, currencySign, currencyBlank) + "]";
                                $("#price_change_" + group_option).html(html);
                            }
                        } else if (document.getElementById("awp_group_" + groupId) && document.getElementById("awp_group_" + groupId).options)
                        {
                            var currentSB = document.getElementById("awp_group_" + groupId);
                            var sb_index = 0;
                            var tmp_index = 0;
                            var cur_class = "";
                            $("#awp_group_" + groupId + " option").each(function () {
                                if (group_option == $(this).val())
                                {
                                    sb_index = tmp_index;
                                    cur_class = $(this).attr('class');
                                }
                                tmp_index++;
                            });
                            var current = document.getElementById("awp_group_" + groupId).options[sb_index].text;



                            /* if (!first)
                             //	alert(id_group+" -> "+id_attribute+" ["+sb_index+"]>> "+current_impact +" - "+ itr_impact + " ("+current+") == " +html);
                             //alert(id_attribute + " = " + sb_index + " = " + current + " | "+ html); */
                            if (current.lastIndexOf("[") > 0)
                                current = current.substring(0, current.lastIndexOf("["));
                            /* If the temp attribute is in bothConnectedAttributes then update the price*/
                            if (awp_in_array(group_option, bothConnectedAttributes)) {
                                connectedPrice = connectedAttributes[validComb]['price'];
                                if (typeof displayPrice != 'undefined' && displayPrice != 1 && awp_no_tax_impact)
                                {
                                    connectedPrice *= 1 + (taxRate / 100);
                                    /* alert("1.5) "+displayPrice+" =  "+awp_new_price + " / " + taxRate); */
                                }

                                connectedPrice *= (100-reduction_percent ) / 100;

                                /* Add the temp connected attribute impact to the total price */
                                //awp_new_price = parseFloat(itr_impact) + parseFloat(connectedPrice);

                                awp_new_price =  awpComputePriceTotalPerChar(validComb, connectedPrice,  itr_impact);     
       
                                //console.log("DROPDOWN groupId = " +groupId + " group_option = " + group_option + " itr_impact = " + itr_impact + " + price = " + connectedAttributes[validComb]['price']);

                                html = "[" + awpFormatCurrency(awp_new_price, currencyFormat, currencySign, currencyBlank) + "]";

                                $("#awp_group_" + groupId + " option:eq(" + sb_index + ")").text(current + html);
                                //document.getElementById("awp_group_"+groupId).selectedIndex = selected;
                                /* alert("Select box " + split[4] + " -- " + current+html); */
                                //select_group = true;
                            }
                        }

                    } else {
                        /* Temp connected attribute is not valid and the product connected attributes does not match any not connected attributes
                         * 	display [---] next to that combination
                         */
                        if (!containsSearchAll) {

                            productPriceNew = productPrice - parseFloat(defaultConnectedAttribute['price']);

                            itr_impact = awp_not_available;
                            if ($("#price_change_" + group_option).length != 0)
                            {

                                if (awp_in_array(group_option, bothConnectedAttributes)) {
                                    awp_new_price = itr_impact;
                                    html = "[" + awp_new_price + "]";
                                    $("#price_change_" + group_option).html(html);
                                }
                            } else if (document.getElementById("awp_group_" + groupId) && document.getElementById("awp_group_" + groupId).options)
                            {
                                var currentSB = document.getElementById("awp_group_" + groupId);
                                var sb_index = 0;
                                var tmp_index = 0;
                                var cur_class = "";
                                $("#awp_group_" + groupId + " option").each(function () {
                                    if (group_option == $(this).val())
                                    {
                                        sb_index = tmp_index;
                                        cur_class = $(this).attr('class');
                                    }
                                    tmp_index++;
                                });
                                var current = document.getElementById("awp_group_" + groupId).options[sb_index].text;
                                if (current.lastIndexOf("[") > 0)
                                    current = current.substring(0, current.lastIndexOf("["));

                                if (awp_in_array(group_option, bothConnectedAttributes)) {
                                    awp_new_price = itr_impact;
                                    html = "[" + awp_new_price + "]";

                                    $("#awp_group_" + groupId + " option:eq(" + sb_index + ")").text(current + html);

                                }
                            }
                        }
                    }
                });

            }


            bothConnectedGroups = $.arrayIntersect(connectedGroups, notConnectedGroups);


            connectedGroupsAffectsImpact = false;
            if (bothConnectedGroups.length == notConnectedGroups.length) {
                groupNotConnectedImpact = bothConnectedGroups;
                connectedGroupsAffectsImpact = true;
            }


            /* Parse all attribute groups which are not connected and compute total price */
            for (idGroup in groupNotConnectedImpact) {
                $("input[name='awp_group_" + idGroup + "'], select[name='awp_group_" + idGroup + "'] option, input:text[class='awp_attribute_selected awp_group_class_" + idGroup + "'], textarea[class='awp_attribute_selected awp_group_class_" + idGroup + "'], input:text[alt='awp_group_" + idGroup + "'], input:hidden[class='awp_attribute_selected awp_group_class_" + idGroup + "']").each(function () {

                    id = $(this).attr('id');
                    /* Get id_attribute for not connected attribute groups */
                    if ($(this).attr('id') && id.indexOf('awp_textbox_group_') > -1) {
                        var group_optionNotConn = $(this).attr('id');
                        group_optionNotConn = group_optionNotConn.replace("awp_textbox_group_", "");
                    } else if ($(this).attr('id') && id.indexOf('awp_textarea_group_') > -1) {
                        var group_optionNotConn = $(this).attr('id');
                        group_optionNotConn = group_optionNotConn.replace("awp_textarea_group_", "");
                    } else if ($(this).attr('id') && id.indexOf('awp_quantity_group_') > -1) {
                        var group_optionNotConn = $(this).attr('id');
                        group_optionNotConn = group_optionNotConn.replace("awp_quantity_group_", "");
                    } else if ($(this).attr('id') && id.indexOf('awp_file_group_') > -1) {
                        var group_optionNotConn = $(this).attr('id');
                        group_optionNotConn = group_optionNotConn.replace("awp_file_group_", "");
                    } else {
                        var group_optionNotConn = $(this).val();
                    }

                    /* Compute temporary combination for both connected and not connected attributes */
                    var tmp_choice = new Array();
                    var tmp_choice_notConn = new Array();

                    for (var key in choice) {

                        if(!isValid(choice[key])) {
                            continue;
                        }
                        if (key == idGroup) {
                            if (connectedGroups.indexOf(key) >= 0) {



                                tmp_choice[key] = new Array();
                                tmp_choice[key].push(group_optionNotConn);
                            }
                            if (notConnectedGroups.indexOf(key) >= 0) {

                                tmp_choice_notConn[key] = new Array();
                                tmp_choice_notConn[key].push(group_optionNotConn);
                            }
                        } else {
                            if (connectedGroups.indexOf(key) >= 0) {



                                tmp_choice[key] = new Array();
                                tmp_choice[key].push(choice[key].toString());
                            }
                            if (notConnectedGroups.indexOf(key) >= 0) {
                                tmp_choice_notConn[key] = new Array();
                                tmp_choice_notConn[key].push(choice[key].toString());


                            }
                        }
                    }


                    var tmp_choice2 = new Array();
                    for (var key in tmp_choice) {
                        for (var key2 in tmp_choice[key]) {
                            tmp_choice2.push(tmp_choice[key][key2].toString());
                        }
                    }

                    tmp_choice = tmp_choice2;

                    var tmp_choice_notConn1 = new Array();
                    for (var key in tmp_choice_notConn) {
                        for (var key2 in tmp_choice_notConn[key]) {
                            tmp_choice_notConn1.push(tmp_choice_notConn[key][key2].toString());
                        }
                    }

                    tmp_choice_notConn = tmp_choice_notConn1;


                    /* Get the valid combination based on temp attribute selection (connected attribute) */
                    var validComb = 0;
                    for (var combinationA in connectedAttributes) {
                        if (arrayContainsAnotherArray(tmp_choice, connectedAttributes[combinationA]['attributes'])) {
                            valid = true;
                            validComb = combinationA;

                        }
                    }
                    /* Add for all not connected attribute the price impact of the connected attribute */

                    if (validComb > 0)
                        price = connectedAttributes[validComb]['price'];
                    else
                        price = 0;


                    var itr_impact = 0;
                    /* Get impact of temp not connected attribute*/
                    for (var k in tmp_choice_notConn) {
                        if (validComb > 0 && connectedGroupsAffectsImpact)
                            itr_impact += 0;
                        else
                        if (typeof awp_impact_list[tmp_choice_notConn[k]] != 'undefined') {
                            itr_impact += parseFloat(awp_impact_list[tmp_choice_notConn[k]]) * currencyRate;

                        }
                    }



                    connectedPrice = price;
                    defaultConnectedAttributeTax = defaultConnectedAttribute['price'];
                    if (typeof displayPrice != 'undefined' && displayPrice != 1 && awp_no_tax_impact)
                    {
                        connectedPrice *= 1 + (taxRate / 100);
                        defaultConnectedAttributeTax *= 1 + (taxRate / 100);
                        /* alert("1.5) "+displayPrice+" =  "+awp_new_price + " / " + taxRate); */
                    }
                    connectedPrice *= (100-reduction_percent ) / 100;
                    price = connectedPrice;


                    /* Add product price - default combination price (if default combination is connected)*/
                    productPriceNew = parseFloat(productPrice) - parseFloat(defaultConnectedAttributeTax);



                    if (typeof displayPrice != 'undefined' && displayPrice != 1 && awp_no_tax_impact)
                    {
                        itr_impact *= 1 + (taxRate / 100);

                    }

                    itr_impact += parseFloat(productPriceNew);




                    if ($("#price_change_" + group_optionNotConn).length != 0)
                    {


                        awp_new_price = parseFloat(itr_impact) + parseFloat(price);
                        html = "[" + awpFormatCurrency(awp_new_price, currencyFormat, currencySign, currencyBlank) + "]";
                        $("#price_change_" + group_optionNotConn).html(html);
                    } else if (document.getElementById("awp_group_" + idGroup) && document.getElementById("awp_group_" + idGroup).options)
                    {
                        var currentSB = document.getElementById("awp_group_" + idGroup);
                        var sb_index = 0;
                        var tmp_index = 0;
                        var cur_class = "";
                        $("#awp_group_" + idGroup + " option").each(function () {
                            if (group_optionNotConn == $(this).val())
                            {
                                sb_index = tmp_index;
                                cur_class = $(this).attr('class');
                            }
                            tmp_index++;
                        });
                        var current = document.getElementById("awp_group_" + idGroup).options[sb_index].text;

                        if (current.lastIndexOf("[") > 0)
                            current = current.substring(0, current.lastIndexOf("["));

                        awp_new_price = parseFloat(itr_impact) + parseFloat(price);
                        html = "[" + awpFormatCurrency(awp_new_price, currencyFormat, currencySign, currencyBlank) + "]";

                        $("#awp_group_" + idGroup + " option:eq(" + sb_index + ")").text(current + html);

                    }
                });


            }

        }

        try
        {
            $.uniform.update();
        } catch (err)
        {
        }
        /* Current selection is valid - show / hide all unavailable options based on the current selection */
        if (found) {

            var new_choice = new Array();
            $.each(choice, function (key, value)
            {
                new_choice.push(value);
            });

            //choice = new_choice;

            new_choice = awp_selected_groups_multiple;

            var connectedGroups = new Array();

            for (var combinationA in connectedAttributes) {
                for (var connectedGroup in connectedAttributes[combinationA]['attributes_to_groups']) {
                    connectedGroups.push(connectedGroup);
                }
            }

            var tmp_valid_choice = new Array();
            for (var key in new_choice) {
                for (var key2 in new_choice[key]) {
                    if (awp_in_array(key, connectedGroups))
                        tmp_valid_choice.push(new_choice[key][key2].toString());
                }
            }
            var valid = false;
            var validComb = 0;
            for (var combinationA in connectedAttributes) {
                if (arrayContainsAnotherArray(tmp_valid_choice, connectedAttributes[combinationA]['attributes'])) {
                    valid = true;
                    validComb = combinationA;

                }
            }
            var connectedReference = '';
            for (var combination in combinations)
            {
                if (combinations[combination]['idCombination'] == validComb) {
                    connectedReference = combinations[combination]['reference'];
                }

            }

            $('#product_reference').find('span').html(connectedReference);
            //console.log(combinations);
            //console.log(combinations[validComb]);
            //console.log('choice');
            //console.log(choice);
            //console.log('awp_selected_groups_multiple');
            //console.log(awp_selected_groups_multiple);
            //console.log('connectedGroups');
            //console.log(connectedGroups);

            if (awp_valid_arr.length > 0) {
                for (var groupId in awp_valid_arr) {

                    //console.log('__________________________________');
                    //console.log(' groupId ' + groupId);
                    /* Checkbox */
                    var validDoNotHide = false;
                    if (awp_connected_do_not_hide[groupId] == 1)
                        validDoNotHide = true;
                    /* If current group is in the not connected groups DO NOT HIDE */
                    if (awp_in_array(groupId, notConnectedGroups))
                        validDoNotHide = true;
                    if (!validDoNotHide)
                    {


                        /* Re-center images */
                        var i = awp_center_images_done.indexOf(groupId);
                        awp_center_images_done.splice(i, 10);
                        awp_center_images_done = new Array()

                        if (awp_group_type[groupId] != "dropdown")
                            awp_center_images(groupId);
                        $("input:checkbox[name='awp_group_" + groupId + "']").each(function () {
                            var group_option = $(this).val();
                            var tmp_choice = new Array();
                            var pos = 0;
                            var group_parent = $(this).attr('name');
                            for (var key in choice) {
                                if (key == groupId) {
                                    tmp_choice[key] = new Array();
                                    tmp_choice[key].push(group_option);
                                } else {
                                    if (connectedGroups.indexOf(key) >= 0) {
                                        tmp_choice[key] = new Array();
                                        tmp_choice[key].push(choice[key].toString());
                                    }
                                }
                            }


                            var tmp_choice2 = new Array();
                            for (var key in tmp_choice) {
                                for (var key2 in tmp_choice[key]) {
                                    tmp_choice2.push(tmp_choice[key][key2].toString());
                                }
                            }

                            tmp_choice = tmp_choice2;

                            var valid = false;

                            for (var combinationA in connectedAttributes) {
                                if (arrayContainsAnotherArray(tmp_choice, connectedAttributes[combinationA]['attributes'])) {
                                    if ((!allowBuyWhenOutOfStock && connectedAttributes[combinationA]['quantity'] > 0) || allowBuyWhenOutOfStock) {
                                        valid = true;
                                    }
                                }
                            }

                            if (valid) {
                                if (awp_out_of_stock == 'hide') {
                                    $('#awp_cell_cont_' + group_option).css('display', 'block');

                                    try
                                    {
                                        $.uniform.update();
                                    } catch (err)
                                    {
                                    }

                                } else if (awp_out_of_stock == 'disable') {

                                    $('#awp_radio_group_' + group_option).removeAttr('disabled');

                                    $('#awp_checkbox_cell' + group_option).removeAttr('disabled');
                                    $('#awp_checkbox_group_' + group_option).removeAttr('disabled');

                                    $('#awp_cell_' + group_option).enable();
                                    try
                                    {
                                        $.uniform.update();
                                    } catch (err)
                                    {
                                    }
                                }
                            } else {
                                if (awp_out_of_stock == 'hide') {

                                    $('#awp_cell_cont_' + group_option).css('display', 'none');

                                    $('#awp_checkbox_group_' + group_option).prop('checked', false);

                                    $('#awp_checkbox_group_' + group_option).removeAttr('checked');
                                    $('#awp_checkbox_group_' + group_option).attr('checked', false);

                                    $('#awp_checkbox_group_' + group_option).removeAttr('checked');

                                    try
                                    {
                                        $.uniform.update();
                                    } catch (err)
                                    {
                                    }
                                } else if (awp_out_of_stock == 'disable') {


                                    $('#awp_checkbox_group_' + group_option).prop('checked', false);

                                    $('#awp_checkbox_group_' + group_option).removeAttr('checked');
                                    $('#awp_checkbox_group_' + group_option).attr('checked', false);


                                    $('#awp_checkbox_group_' + group_option).prop('checked', false);

                                    $('#awp_checkbox_group_' + group_option).removeAttr('checked');
                                    $('#awp_checkbox_group_' + group_option).attr('checked', false);


                                    $('#awp_radio_group_' + group_option).attr('disabled', 'true');

                                    $('#awp_checkbox_cell' + group_option).attr('disabled', 'true');
                                    $('#awp_checkbox_group_' + group_option).attr('disabled', 'true');

                                    $('#awp_cell_' + group_option).disable();

                                    try
                                    {
                                        $.uniform.update();
                                    } catch (err)
                                    {
                                    }
                                }
                            }

                        });
                        /* END Checkbox */

                        /* Radio option & image single select*/
                        $('input[name="awp_group_' + groupId + '"]').not('input[type="checkbox"]').each(function ()
                        {
                            var group_option = $(this).val();
                            var tmp_choice = new Array();
                            var pos = 0;
                            var group_parent = $(this).attr('name');

                            for (var key in choice) {
                                if (key == groupId) {

                                    tmp_choice[key] = group_option.toString();
                                } else {

                                    if (connectedGroups.indexOf(key) >= 0)
                                        tmp_choice[key] = choice[key].toString();
                                }
                            }

                            var valid = false;

                            for (var combinationA in connectedAttributes) {
                                if (arrayContainsAnotherArray(tmp_choice, connectedAttributes[combinationA]['attributes'])) {
                                    if ((!allowBuyWhenOutOfStock && connectedAttributes[combinationA]['quantity'] > 0) || allowBuyWhenOutOfStock) {
                                        valid = true;
                                    }
                                }
                            }

                            if (valid) {
                                if (awp_out_of_stock == 'hide') {
                                    $('#awp_cell_cont_' + group_option).css('display', 'block');
                                } else if (awp_out_of_stock == 'disable') {
                                    $('#awp_radio_group_' + group_option).removeAttr('disabled');
                                    $('#awp_cell_' + group_option).enable();
                                }
                            } else {
                                if (awp_out_of_stock == 'hide') {
                                    $('#awp_cell_cont_' + group_option).css('display', 'none');
                                } else if (awp_out_of_stock == 'disable') {
                                    $('#awp_radio_group_' + group_option).attr('disabled', 'true');
                                    $('#awp_cell_' + group_option).disable();
                                }
                            }

                        });
                        /* END Radio button */


                        /* Dropdown */
                        $('select[name="awp_group_' + groupId + '"] option').each(function ()
                        {
                            var group_option = $(this).val();
                            var tmp_choice = new Array();
                            var pos = 0;
                            var group_parent = $(this).attr('name');
                            var group_option_element = $('select[name="awp_group_' + groupId + '"]' + ' option[value="' + group_option + '"]');
                            //console.log('!!!!!!!!!!!!!! DROPDOWN !!!!!!!!!!!');
                            //console.log(' group_option ' + group_option);
                            for (var key in choice) {
                                if(!isValid(choice[key])) {
                                    continue;
                                }

                                if (key == groupId) {
                                    tmp_choice[key] = group_option.toString();
                                } else {
                                    if (connectedGroups.indexOf(key) >= 0)
                                        tmp_choice[key] = choice[key].toString();
                                }
                            }

                            var valid = false;

                            for (var combinationA in connectedAttributes) {
                                if (arrayContainsAnotherArray(tmp_choice, connectedAttributes[combinationA]['attributes'])) {
                                    if ((!allowBuyWhenOutOfStock && connectedAttributes[combinationA]['quantity'] > 0) || allowBuyWhenOutOfStock) {
                                        valid = true;
                                    }
                                }
                            }

                            if (valid) {
                                if (awp_out_of_stock == 'hide') {

                                    if (isIE && group_option_element.parent().is('span'))
                                        group_option_element.unwrap().show();
                                    else
                                        group_option_element.show();

                                } else if (awp_out_of_stock == 'disable') {
                                    group_option_element.removeAttr('disabled');

                                }
                            } else {
                                if (awp_out_of_stock == 'hide') {
                                    group_option_element.hide();
                                    if (isIE && !group_option_element.parent().is('span'))
                                        group_option_element.wrap('<span>').hide();
                                } else if (awp_out_of_stock == 'disable') {
                                    group_option_element.attr('disabled', 'true');
                                }
                            }

                        });
                        /* END Dropdown */

                        /* Textbox*/
                        $("input:text[class='awp_attribute_selected awp_group_class_" + groupId + "']").each(function () {
                            var group_option = $(this).attr('id');
                            group_option = group_option.replace("awp_textbox_group_", "");

                            var tmp_choice = new Array();
                            var pos = 0;
                            var group_parent = $(this).attr('name');
                            for (var key in choice) {
                                if (key == groupId) {
                                    tmp_choice[key] = new Array();
                                    tmp_choice[key].push(group_option);
                                } else {
                                    if (connectedGroups.indexOf(key) >= 0) {
                                        tmp_choice[key] = new Array();
                                        tmp_choice[key].push(choice[key].toString());
                                    }
                                }
                            }

                            var tmp_choice2 = new Array();
                            for (var key in tmp_choice) {
                                for (var key2 in tmp_choice[key]) {
                                    tmp_choice2.push(tmp_choice[key][key2].toString());
                                }
                            }

                            tmp_choice = tmp_choice2;

                            var valid = false;

                            for (var combinationA in connectedAttributes) {
                                if (arrayContainsAnotherArray(tmp_choice, connectedAttributes[combinationA]['attributes'])) {
                                    if ((!allowBuyWhenOutOfStock && connectedAttributes[combinationA]['quantity'] > 0) || allowBuyWhenOutOfStock) {
                                        valid = true;
                                    }
                                }
                            }

                            if (valid) {
                                if (awp_out_of_stock == 'hide') {
                                    $('#awp_cell_cont_' + group_option).css('display', 'block');

                                } else if (awp_out_of_stock == 'disable') {
                                    $('#awp_textbox_group_' + group_option).removeAttr('disabled');
                                    $('#awp_textbox_group_' + group_option).enable();
                                }
                            } else {
                                if (awp_out_of_stock == 'hide') {
                                    $('#awp_cell_cont_' + group_option).css('display', 'none');
                                    $('#awp_textbox_group_' + group_option).val('');

                                } else if (awp_out_of_stock == 'disable') {
                                    $('#awp_textbox_group_' + group_option).val('');
                                    $('#awp_textbox_group_' + group_option).attr('disabled', 'true');
                                    $('#awp_textbox_group_' + group_option).disable();
                                }
                            }

                        });
                        /* END Textbox */


                        /* Textarea*/
                        $("textarea[class='awp_attribute_selected awp_group_class_" + groupId + "']").each(function () {
                            var group_option = $(this).attr('id');
                            group_option = group_option.replace("awp_textarea_group_", "");

                            var tmp_choice = new Array();
                            var pos = 0;
                            var group_parent = $(this).attr('name');
                            for (var key in choice) {
                                if (key == groupId) {
                                    tmp_choice[key] = new Array();
                                    tmp_choice[key].push(group_option);
                                } else {
                                    if (connectedGroups.indexOf(key) >= 0) {
                                        tmp_choice[key] = new Array();
                                        tmp_choice[key].push(choice[key].toString());
                                    }
                                }
                            }

                            var tmp_choice2 = new Array();
                            for (var key in tmp_choice) {
                                for (var key2 in tmp_choice[key]) {
                                    tmp_choice2.push(tmp_choice[key][key2].toString());
                                }
                            }

                            tmp_choice = tmp_choice2;

                            var valid = false;

                            for (var combinationA in connectedAttributes) {
                                if (arrayContainsAnotherArray(tmp_choice, connectedAttributes[combinationA]['attributes'])) {
                                    if ((!allowBuyWhenOutOfStock && connectedAttributes[combinationA]['quantity'] > 0) || allowBuyWhenOutOfStock) {
                                        valid = true;
                                    }
                                }
                            }

                            if (valid) {
                                if (awp_out_of_stock == 'hide') {
                                    $('#awp_cell_cont_' + group_option).css('display', 'block');

                                } else if (awp_out_of_stock == 'disable') {
                                    $('#awp_textarea_group_' + group_option).removeAttr('disabled');
                                    $('#awp_textarea_group_' + group_option).enable();
                                }
                            } else {
                                if (awp_out_of_stock == 'hide') {
                                    $('#awp_cell_cont_' + group_option).css('display', 'none');
                                    $('#awp_textarea_group_' + group_option).val('');

                                } else if (awp_out_of_stock == 'disable') {
                                    $('#awp_textarea_group_' + group_option).val('');
                                    $('#awp_textarea_group_' + group_option).attr('disabled', 'true');
                                    $('#awp_textarea_group_' + group_option).disable();
                                }
                            }

                        });
                        /* END Textarea */



                        /* FileUpload*/
                        $("input:hidden[class='awp_attribute_selected awp_group_class_" + groupId + "']").each(function () {
                            var group_option = $(this).attr('id');
                            group_option = group_option.replace("awp_file_group_", "");

                            var tmp_choice = new Array();
                            var pos = 0;
                            var group_parent = $(this).attr('name');
                            for (var key in choice) {
                                if (key == groupId) {
                                    tmp_choice[key] = new Array();
                                    tmp_choice[key].push(group_option);
                                } else {
                                    if (connectedGroups.indexOf(key) >= 0) {
                                        tmp_choice[key] = new Array();
                                        tmp_choice[key].push(choice[key].toString());
                                    }
                                }
                            }

                            var tmp_choice2 = new Array();
                            for (var key in tmp_choice) {
                                for (var key2 in tmp_choice[key]) {
                                    tmp_choice2.push(tmp_choice[key][key2].toString());
                                }
                            }

                            tmp_choice = tmp_choice2;

                            var valid = false;

                            for (var combinationA in connectedAttributes) {
                                if (arrayContainsAnotherArray(tmp_choice, connectedAttributes[combinationA]['attributes'])) {
                                    if ((!allowBuyWhenOutOfStock && connectedAttributes[combinationA]['quantity'] > 0) || allowBuyWhenOutOfStock) {
                                        valid = true;
                                    }
                                }
                            }

                            if (valid) {
                                if (awp_out_of_stock == 'hide') {
                                    $('#awp_cell_cont_' + group_option).css('display', 'block');

                                } else if (awp_out_of_stock == 'disable') {
                                    $('#upload_button_' + group_option).removeAttr('disabled');
                                    $('#upload_button_' + group_option).enable();
                                }
                            } else {
                                if (awp_out_of_stock == 'hide') {
                                    $('#awp_cell_cont_' + group_option).css('display', 'none');
                                    $('#awp_image_cell_' + group_option).html('');

                                    $('#awp_file_group_' + group_option).val('');


                                } else if (awp_out_of_stock == 'disable') {

                                    $('#awp_image_cell_' + group_option).html('');

                                    $('#awp_file_group_' + group_option).val('');

                                    $('#upload_button_' + group_option).attr('disabled', 'true');
                                    $('#upload_button_' + group_option).disable();
                                }
                            }

                        });
                        applyFileUpload();
                        /* END FileUpload */

                        /* Quantity*/
                        $("input:text[alt='awp_group_" + groupId + "']").each(function () {
                            var group_option = $(this).attr('id');
                            group_option = group_option.replace("awp_quantity_group_", "");

                            var tmp_choice = new Array();
                            var pos = 0;
                            var group_parent = $(this).attr('name');
                            for (var key in choice) {
                                if (key == groupId) {
                                    tmp_choice[key] = new Array();
                                    tmp_choice[key].push(group_option);
                                } else {
                                    if (connectedGroups.indexOf(key) >= 0) {
                                        tmp_choice[key] = new Array();
                                        tmp_choice[key].push(choice[key].toString());
                                    }
                                }
                            }

                            var tmp_choice2 = new Array();
                            for (var key in tmp_choice) {
                                for (var key2 in tmp_choice[key]) {
                                    tmp_choice2.push(tmp_choice[key][key2].toString());
                                }
                            }

                            tmp_choice = tmp_choice2;

                            var valid = false;

                            for (var combinationA in connectedAttributes) {
                                if (arrayContainsAnotherArray(tmp_choice, connectedAttributes[combinationA]['attributes'])) {
                                    if ((!allowBuyWhenOutOfStock && connectedAttributes[combinationA]['quantity'] > 0) || allowBuyWhenOutOfStock) {
                                        valid = true;
                                    }
                                }
                            }

                            if (valid) {
                                if (awp_out_of_stock == 'hide') {
                                    $('#awp_cell_cont_' + group_option).css('display', 'block');

                                } else if (awp_out_of_stock == 'disable') {
                                    $('#awp_quantity_group_' + group_option).removeAttr('disabled');
                                    $('#awp_quantity_group_' + group_option).enable();
                                }
                            } else {
                                if (awp_out_of_stock == 'hide') {
                                    $('#awp_cell_cont_' + group_option).css('display', 'none');


                                    $('#awp_quantity_group_' + group_option).val('0');


                                } else if (awp_out_of_stock == 'disable') {


                                    $('#awp_quantity_group_' + group_option).val('0');

                                    $('#awp_quantity_group_' + group_option).attr('disabled', 'true');
                                    $('#awp_quantity_group_' + group_option).disable();
                                }
                            }

                        });
                        /* END Quantity  */
                    }

                }
            }
        }
    }

    // show images bound to the selected combination
    global_validComb = validComb;
    if (!first) {
        show_cover_thumbnails(validComb);
    }
    // end images

    if (awp_group_type[group] != "dropdown")
        awp_center_images(group);
    else
        $(".awp_cell_cont_" + group).css('width', '100%');


    if (document.getElementById('awp_price'))
        $("#awp_price").html($("#our_price_display").html())
    if (document.getElementById('awp_second_price'))
        $("#awp_second_price").html($("#our_price_display").html())
    /* update URL */
    var prices = awp_get_total_prices(0, 0);
    /*END Connected attributes */
    if (first) {
        checkUrl();
    }
    if (typeof getProductAttribute != 'undefined')
    {
        getProductAttribute();
    }
}


function show_cover_thumbnails(validComb) {
    if (awp_isQuickView) {
        var url = $('#awpQuickViewProductLink').val();
    } else {
        var url = prestashop.urls.current_url;
    }

    $.post(url, {
        ajax: "1",
        action: "refresh",
        id_product_attribute: validComb || 0 // since URL doesn't contain valid combination id
    }, null, "json").then(function (r) {
        $(".images-container").replaceWith(r.product_cover_thumbnails);
         prestashop.emit("updatedProduct", r);
         var prices = awp_get_total_prices(0,  0);
    });
}

/*  Start Connected attributes */
function awp_array_key(val, arr)
{
    for (var m = 0; m < arr.length; m++)
    {
        if (arr[m] == val)
            return m;
    }
    return -1;
}
function awp_in_array(val, arr)
{
    for (var m = 0; m < arr.length; m++)
    {
        if (arr[m] == val)
            return true;
    }
    return false;
}

function countArrayInArray(needle, haystack) {
    var count = 0;

    for (index in needle) {
        for (index2 in needle[index]) {
            if (haystack.indexOf(needle[index][index2]) >= 0) {
                count++;

            }
        }
    }
    return count;
}
function arrayContainsAnotherArray(needle, haystack) {
    for (index in needle) {

        if (haystack.indexOf(needle[index]) === -1)
            return false;
    }
    return true;
}

$.arrayIntersect = function (a, b)
{
    return $.grep(a, function (i)
    {
        return $.inArray(i, b) > -1;
    });
};

function toUnique(a, b, c) {
    b = a.length;
    while (c = --b)
        while (c--)
            a[b] !== a[c] || a.splice(c, 1);
    return a
}

/* ==== jquery.enableDisable.js ==== */
// enable and disable plugins. Enable or disable links, buttons, etc.
(function ($) {
    $.fn.disable = function () {
        return $(this).each(function () {
            switch ($(this)[0].nodeName.toUpperCase()) {
                case "A":
                    jQuery.data($(this)[0], "href", $(this).attr("href"));
                    $(this).removeAttr('href');
                case "DIV":
                    jQuery.data($(this)[0], "onclick", $(this).attr("onclick"));
                    $(this).removeAttr('onclick');
                default:
                    $(this).attr('disabled', 'disabled');
            }
        });
    };
    $.fn.enable = function () {
        return $(this).each(function () {
            switch ($(this)[0].nodeName.toUpperCase()) {
                case "A":
                    if (typeof jQuery.data($(this)[0], "href") != "undefined") {
                        $(this).attr("href", jQuery.data($(this)[0], "href"));
                    }
                case "DIV":
                    if (typeof jQuery.data($(this)[0], "onclick") != "undefined") {
                        $(this).attr("onclick", jQuery.data($(this)[0], "onclick"));
                    }

                default:
                    $(this).removeAttr('disabled').removeClass("disabled");
            }
        });
    };
})(jQuery);
/* End connected attributes */

function awp_get_total_prices(awp_extra, tmp_productPriceWithoutReduction)
{
    var doa = false;
    productPriceWithoutReduction = tmp_productPriceWithoutReduction;
    awp_extra = parseFloat(awp_extra);
    var name = document.awp_wizard;
    var awp_total_impact = 0;
    var awp_total_weight = 0;
    var awp_total_impact_quantity = new Array();
    var awp_total_weight_quantity = new Array();
    var awp_total_quantity_quantity = new Array();
    var awp_quantity = 0;
    var awp_quantity_default = 0;
    var awp_min_quantity = 1;
    var awp_min_quantity_default = 1;
    var awp_first_default = true;
    var awp_first = true;
    /* alert ("start awp_get_total_prices"); */
    var loop = 0;

    awp_selected_groups_multiple = new Array();

    /* Connected Attributes */
    var connectedAttrs = new Array();

    for (var combinationA in connectedAttributes) {
        for (var connectedAttrVals in connectedAttributes[combinationA]['attributes']) {
            connectedAttrs.push(connectedAttributes[combinationA]['attributes'][connectedAttrVals]);
        }
    }

    awp_selected_groups_multiple2 = new Array();
    $('.awp_box .awp_attribute_selected').each(function ()
    {
        var e_type = $(this).prop('type');

        if ((e_type != 'radio' || $(this).attr('checked') || $(this).prop('checked')) &&
            (e_type != 'checkbox' || $(this).attr('checked') || $(this).prop('checked')) &&
            (e_type != 'text' || $(this).val() != "0") &&
            $(this).val() != "")
        {
            var awp_arr = $(this).attr('name').split('_');

            var found = false;
            if (e_type != 'text' && e_type != 'textarea')
                for (var key in awp_impact_list)
                {
                    if (key == $(this).val())
                        found = true;
                }

            if (found)
            {
                awp_selected_groups[awp_arr[2]] = $(this).val();
                if (typeof awp_selected_groups_multiple2[awp_arr[2]] != "undefined")
                    awp_selected_groups_multiple2[awp_arr[2]].push($(this).val());
                else
                    awp_selected_groups_multiple2[awp_arr[2]] = new Array($(this).val());
            } else
            {
                if ($(this).val() != "" && awp_arr.length == 4)
                {
                    if (typeof awp_selected_groups_multiple2[awp_arr[2]] != "undefined")
                        awp_selected_groups_multiple2[awp_arr[2]].push(awp_arr[3]);
                    else
                        awp_selected_groups_multiple2[awp_arr[2]] = new Array(awp_arr[3]);
                }
            }
            awp_first_default = false;
        }
    });

    connected = false;

    var connectedGroups = new Array();

    for (var combinationA in connectedAttributes) {
        for (var connectedGroup in connectedAttributes[combinationA]['attributes_to_groups']) {
            connectedGroups.push(connectedGroup);
        }
    }
    var validCombArray = new Array();
    var validGroupArray = new Array();
    for (var groupId in awp_selected_groups_multiple2) {
        for (var idAttr in awp_selected_groups_multiple2[groupId]) {
            if (awp_in_array(groupId, connectedGroups)) {
                validCombArray.push(awp_selected_groups_multiple2[groupId][idAttr]);
                validGroupArray.push(groupId);
            }
        }
    }
    for (var combinationA in connectedAttributes) {
        if (arrayContainsAnotherArray(validCombArray, connectedAttributes[combinationA]['attributes'])) {
            connected = true;
        }
    }
    /* END - Connected Attributes */
    $('.awp_box .awp_attribute_selected').each(function ()
    {
        var e_type = $(this).prop('type');


        if ((e_type != 'radio' || $(this).attr('checked') || $(this).prop('checked')) &&
            (e_type != 'checkbox' || $(this).attr('checked') || $(this).prop('checked')) &&
            (e_type != 'text' || $(this).val() != "0") &&
            $(this).val() != "")
        {
            var awp_arr = $(this).attr('name').split('_');

            var found = false;
            if (e_type != 'text' && e_type != 'textarea')
                for (var key in awp_impact_list)
                {
                    if (key == $(this).val())
                        found = true;
                }

            if (found)
            {
                if (!awp_arr[2] || awp_group_type[awp_arr[2]] != "quantity")
                {
                    /* If current attributes are connected do not add any impact */
                    if (connected && awp_in_array($(this).val(), connectedAttrs)) {
                        awp_total_impact += 0;
                        awp_total_weight += 0;
                    } else {
                        awp_total_impact += parseFloat(awp_impact_list[$(this).val()]);
                        awp_total_weight += parseFloat(awp_weight_list[$(this).val()]);
                    }
                }
                if (awp_first || awp_quantity > parseInt(awp_qty_list[$(this).val()]))
                {
                    /* alert(awp_quantity+" = "+parseInt(awp_qty_list[$(this).val()])+" --- "+$(this).val());*/
                    awp_quantity = parseInt(awp_qty_list[$(this).val()]);
                }
                if (awp_first || awp_min_quantity < parseInt(awp_min_qty[$(this).val()]))
                    awp_min_quantity = parseInt(awp_min_qty[$(this).val()]);
                awp_first = false;
                awp_selected_groups[awp_arr[2]] = $(this).val();
                if (typeof awp_selected_groups_multiple[awp_arr[2]] != "undefined")
                    awp_selected_groups_multiple[awp_arr[2]].push($(this).val());
                else
                    awp_selected_groups_multiple[awp_arr[2]] = new Array($(this).val());
            } else
            {
                if ($(this).val() != "" && awp_arr.length == 4)
                {
                    if (awp_group_type[awp_arr[2]] == "calculation")
                    {
                        alert("calc = " + awp_impact_list[awp_arr[3]] + " * " + $(this).val() + " * " + awp_multiply_list[awp_arr[3]]);
                        awp_total_impact += parseFloat((awp_impact_list[awp_arr[3]] * $(this).val() * awp_multiply_list[awp_arr[3]]) / 1000000);
                        awp_total_weight += parseFloat(awp_weight_list[awp_arr[3]]);
                    } else if ((!awp_arr[2] || awp_group_type[awp_arr[2]] != "quantity") && awp_impact_list[awp_arr[3]])
                    {
                        /* If current attributes are connected do not add any impact */
                        if (connected && awp_in_array(awp_arr[3], connectedAttrs)) {
                            awp_total_impact += 0;
                            awp_total_weight += 0;
                        } else {
                            if (typeof awp_groups_chars[awp_arr[2]] != 'undefined' && awp_groups_chars[awp_arr[2]]['price_impact_per_char'] == 1) {
                                minLimitCharge = awp_groups_chars[awp_arr[2]]['group_min_limit'];
                                if (typeof minLimitCharge == 'undefined' || minLimitCharge == 0 || minLimitCharge < 0) {
                                    $minLimitCharge = 1;
                                }
                                exceptions = awp_groups_chars[awp_arr[2]]['exceptions'];
                                valChars = $(this).val();
                                if (exceptions != '') {
                                    exceptionsArr = exceptions.split('');
                                    for (i in exceptionsArr) {
                                        valChars = valChars.split(exceptionsArr[i]).join('');
                                    }
                                }
                                
                                charsValCount = valChars.length;
                                priceImpactPerChar = 0;
                                if (minLimitCharge < charsValCount) {
                                    priceImpactPerChar = charsValCount * parseFloat(awp_impact_list[awp_arr[3]]);
                                } else {
                                    priceImpactPerChar = parseFloat(minLimitCharge) * parseFloat(awp_impact_list[awp_arr[3]]);
                                }
                                awp_total_impact += priceImpactPerChar;                                

                            } else {
                                awp_total_impact += parseFloat(awp_impact_list[awp_arr[3]]);
                            }
                           
                            awp_total_weight += parseFloat(awp_weight_list[awp_arr[3]]);
                        }
                    }
                    if (awp_first || awp_quantity > parseInt(awp_qty_list[awp_arr[3]]))
                    {
                        /* alert(awp_quantity+" == "+parseInt(awp_qty_list[awp_arr[3]])+" --- "+awp_arr[3]); */
                        awp_quantity = parseInt(awp_qty_list[awp_arr[3]]);
                    }
                    if (awp_first || awp_min_quantity < parseInt(awp_min_qty[awp_arr[3]]))
                        awp_min_quantity = parseInt(awp_min_qty[awp_arr[3]]);
                    awp_first = false;
                    awp_selected_groups[awp_arr[2]] = awp_arr[3];
                    if (typeof awp_selected_groups_multiple[awp_arr[2]] != "undefined")
                        awp_selected_groups_multiple[awp_arr[2]].push(awp_arr[3]);
                    else
                        awp_selected_groups_multiple[awp_arr[2]] = new Array(awp_arr[3]);
                }
            }
            awp_first_default = false;
        } else if ((e_type != 'text' && e_type != 'textarea') || $(this).val() != "")
        {
            var awp_arr = $(this).attr('name').split('_');
            if (awp_first_default && awp_arr.length == 4)
            {
                /* alert(awp_quantity+" === "+parseInt(awp_qty_list[awp_arr[3]])+" --- "+awp_arr[3]); */
                awp_quantity = parseInt(awp_qty_list[awp_arr[3]]);
            }
            if (awp_first_default && awp_arr.length == 4)
                awp_min_quantity = parseInt(awp_min_qty[awp_arr[3]]);
            awp_first_default = false;
        } else if (e_type == 'text' || e_type == 'textarea')
        {
            var awp_arr = $(this).attr('name').split('_');
            if (awp_arr.length == 4)
            {
                /*alert(awp_quantity+" === "+parseInt(awp_qty_list[awp_arr[3]])+" --- "+awp_arr[3]);*/
                awp_quantity_default = parseInt(awp_qty_list[awp_arr[3]]);
            }
            if (awp_arr.length == 4)
                awp_min_quantity_default = parseInt(awp_min_qty[awp_arr[3]]);
        }
    });
    if (awp_first_default)
    {
        awp_quantity = awp_quantity_default;
        awp_min_quantity = awp_min_quantity_default;
    }

    /* Get connected combination id to compute total price */
    var connectedSelVals = new Array();
    for (var k in awp_selected_groups_multiple) {
        for (var j in awp_selected_groups_multiple[k]) {
            connectedSelVals.push(awp_selected_groups_multiple[k][j]);
        }
    }

    onlyConnectedSelVars = $.arrayIntersect(connectedSelVals, connectedAttrs);

    var connectedGroups = new Array();

    for (var combinationA in connectedAttributes) {
        for (var connectedAttrGr in connectedAttributes[combinationA]['id_attribute_groups']) {
            if (!awp_in_array(connectedAttributes[combinationA]['id_attribute_groups'][connectedAttrGr], connectedGroups))
                connectedGroups.push(connectedAttributes[combinationA]['id_attribute_groups'][connectedAttrGr]);
        }
    }
    var idCombOK = 0;
    if (onlyConnectedSelVars.length >= connectedGroups.length)
        for (var k in connectedAttributes) {
            if (arrayContainsAnotherArray(onlyConnectedSelVars, connectedAttributes[k]['attributes'])) {
                idCombOK = k;
            }
        }
    /* End connected attributes */
    var attribut_price_tmp = awp_total_impact;

    if (typeof awp_layered_img_id != 'undefined')
        img_offset = $('.' + awp_layered_img_id).offset();
    /*
     * Change Attribute Layered Images
     */
    if (typeof awp_layered_image_list != 'undefined')
        if (awp_layered_image_list.length > 0)
        {
            for (var key in awp_selected_groups)
            {
                awp_layer_filename = awp_layered_image_list[awp_selected_groups[key]] != '' ? awp_layered_image_list[awp_selected_groups[key]] : false;
                awp_layer_pos = awp_group_order[key];
                if (awp_layer_filename)
                {
                    /*
                     * If checkbox, need to show all the checked boxes's images.
                     */
                    if (awp_group_type[key] == "checkbox")
                    {
                        $('.awp_group_class_' + key).each(function () {
                            var awp_csli = false;

                            var str = $(this).attr('id').split("_");
                            tmp_attr_id = str.pop();

                            /* Check for filename again based on each checkbox element */
                            awp_layer_filename = awp_layered_image_list[tmp_attr_id] != '' ? awp_layered_image_list[tmp_attr_id] : false;
                            /* alert(tmp_attr_id+' -----------> '+awp_layer_filename); */
                            if (awp_layer_filename)
                            {
                                if ($('.awp_liga_' + tmp_attr_id).length)
                                {
                                    /* alert(tmp_attr_id+' --> ' +$(this).attr('checked') + ' --- '+$('.awp_liga_'+tmp_attr_id).hasClass('awp_liga_'+tmp_attr_id)); */
                                    /* If selected attribute and not hidden, show it */
                                    if ($(this).attr('checked') && ($('.awp_liga_' + tmp_attr_id).attr('display') == 'none' || !$('.awp_liga_' + tmp_attr_id).attr('display')))
                                        $('.awp_liga_' + tmp_attr_id).fadeIn('fast');
                                    /* If not selected attribute and not hidden, hide it */
                                    else if (!$(this).attr('checked') && ($('.awp_liga_' + tmp_attr_id).attr('display') != 'none' || !$('.awp_liga_' + tmp_attr_id).attr('display')))
                                        $('.awp_liga_' + tmp_attr_id).fadeOut('fast');
                                    /* if a layer exists and marked, set to true to avoid creating it again */
                                    if ($('.awp_liga_' + tmp_attr_id).attr('display') != 'none')
                                        awp_csli = true;
                                }
                                /* Layered Image was not created yet, create it now */
                                /* alert($(this).attr('id') + " ==== " + $(this).attr('checked')+' ('+awp_csli+')'); */
                                if ($(this).attr('checked') && !awp_csli)
                                {
                                    /* alert('creating ' + tmp_attr_id); */
                                    if ($('#awp_product_image').length)
                                        $('#awp_product_image').prepend('<div class="awp_layered_images awp_lig_' + key + ' awp_liga_' + tmp_attr_id + '" id="awp_attr_img_' + tmp_attr_id + '" style="position: absolute;z-index:' + (1000 + awp_layer_pos) + ';"><img src="' + awp_layer_filename + '" border="0" /></div>');
                                    else if ($('.product-cover').length)
                                        $('.product-cover').prepend('<div class="awp_layered_images awp_lig_' + key + ' awp_liga_' + tmp_attr_id + '" id="awp_attr_img_' + tmp_attr_id + '" style="position: absolute;z-index:' + (1000 + awp_layer_pos) + ';"><img src="' + awp_layer_filename + '" border="0" /></div>');
                                    else
                                        $('body').append('<div class="awp_layered_images awp_lig_' + key + ' awp_liga_' + tmp_attr_id + '" id="awp_attr_img_' + tmp_attr_id + '" style="position: absolute; top:' + img_offset.top + 'px;left:' + img_offset.left + 'px;z-index:' + (1000 + awp_layer_pos) + ';"><img src="' + awp_layer_filename + '" border="0" /></div>');
                                }
                            }
                        });
                    } else
                    {
                        var awp_csli = false;
                        $('.awp_lig_' + key).each(function () {
                            /* If selected attribute and not hidden, show it */
                            if ($(this).hasClass('awp_liga_' + awp_selected_groups[key]) && ($(this).attr('display') == 'none' || !$(this).attr('display')))
                                $(this).fadeIn('fast');
                            /* If not selected attribute and not hidden, hide it */
                            else if (!$(this).hasClass('awp_liga_' + awp_selected_groups[key]) && ($(this).attr('display') != 'none' || !$(this).attr('display')))
                                $(this).fadeOut('fast');
                            /* if a layer exists and marked, set to true to avoid creating it again */
                            if ($(this).hasClass('awp_liga_' + awp_selected_groups[key]) && $(this).attr('display') != 'none')
                                awp_csli = true;
                        });
                        /* Layered Image was not created yet, create it now */
                        if (!awp_csli)
                        {
                            if ($('#awp_product_image').length)
                                $('#awp_product_image').prepend('<div class="awp_layered_images awp_lig_' + key + ' awp_liga_' + awp_selected_groups[key] + '" id="awp_attr_img_' + awp_selected_groups[key] + '" style="position: absolute;z-index:' + (1000 + awp_layer_pos) + ';"><img src="' + awp_layer_filename + '" border="0" /></div>');
                            else if ($('.product-cover').length)
                                $('.product-cover').prepend('<div class="awp_layered_images awp_lig_' + key + ' awp_liga_' + awp_selected_groups[key] + '" id="awp_attr_img_' + awp_selected_groups[key] + '" style="position: absolute;z-index:' + (1000 + awp_layer_pos) + ';"><img src="' + awp_layer_filename + '" border="0" /></div>');
                            else
                                $('body').append('<div class="awp_layered_images awp_lig_' + key + ' awp_liga_' + awp_selected_groups[key] + '" id="awp_attr_img_' + awp_selected_groups[key] + '" style="position: absolute; top:' + img_offset.top + 'px;left:' + img_offset.left + 'px;z-index:' + (1000 + awp_layer_pos) + ';"><img src="' + awp_layer_filename + '" border="0" /></div>');
                        }
                    }
                }
            }
        }

    var tax = noTaxForThisProduct ? 1 : ((taxRate / 100) + 1);

    if (typeof productReference != 'undefined' && productReference != '')
    {
        $('#product_reference').show();
        if ($('#product_reference span').length)
            $('#product_reference span').text(productReference);
    }

    new_choice = awp_selected_groups_multiple;

    var connectedGroups = new Array();

    for (var combinationA in connectedAttributes) {
        for (var connectedGroup in connectedAttributes[combinationA]['attributes_to_groups']) {
            connectedGroups.push(connectedGroup);
        }
    }
    var tmp_valid_choice = new Array();
    for (var key in new_choice) {
        for (var key2 in new_choice[key]) {
            if (awp_in_array(key, connectedGroups))
                tmp_valid_choice.push(new_choice[key][key2].toString());
        }
    }

    var valid = false;
    var validComb = 0;
    for (var combinationA in connectedAttributes) {
        if (arrayContainsAnotherArray(tmp_valid_choice, connectedAttributes[combinationA]['attributes'])) {
            valid = true;
            validComb = combinationA;
        }
    }

    var connectedReference = '';
    for (var combination in combinations)
    {
        if (combinations[combination]['idCombination'] == validComb) {
            connectedReference = combinations[combination]['reference'];
        }
    }

    if (connectedReference != '')
        $('#product_reference').find('span').html(connectedReference);

    /* if the product contains attribute values from a single attribute group */
    if (singleAttributeGroup) {
        /* check if options are single select values and update the reference based on the selection */
        attrType = $('.awp_attribute_selected').is('input[type="radio"], select');
        if (attrType) {
            var single_valid_choice = new Array();
            for (var key in new_choice) {
                for (var key2 in new_choice[key]) {
                    single_valid_choice.push(parseInt(new_choice[key][key2]));
                }
            }

            var singleValid = false;
            var singleValidComb = 0;
            var singleValidCombA = 0;
            for (var combinationA in combinations) {
                if (arrayContainsAnotherArray(single_valid_choice, combinations[combinationA]['attributes'])) {
                    singleValid = true;
                    singleValidComb = combinations[combinationA]['idCombination'];
                    singleValidCombA = combinationA;
                }
            }

            if (singleValid)
                $('#product_reference').find('span').html(combinations[singleValidCombA]['reference']);
        }
    }


    /* retrieve price without group_reduction in order to compute the group reduction after
     // the specific price discount (done in the JS in order to keep backward compatibility) */
    var priceTaxExclWithoutGroupReduction = ps_round(productPriceTaxExcluded, 6) * (1 / awp_group_reduction);
    /* PS 1.6 revesred the way group discount is used) */

    priceTaxExclWithoutGroupReduction = ps_round(productPriceTaxExcluded, 6);
    /*alert(priceTaxExclWithoutGroupReduction + " = " + productPriceTaxExcluded + " * 1 / " + awp_group_reduction );*/
    var tax = (taxRate / 100) + 1;

    var display_specific_price;
    if (typeof product_specific_price != 'undefined') {
        display_specific_price = product_specific_price['price'];
        if (product_specific_price['reduction_type'] == 'percentage') {
            discountPercentage = product_specific_price.reduction * 100;

            var toFix = 2;
            if ((parseFloat(discountPercentage).toFixed(2) - parseFloat(discountPercentage).toFixed(0)) == 0)
                toFix = 0;

            $('#reduction_percent_display').html((parseFloat(discountPercentage).toFixed(toFix)) + '%');

        }
        /* alert("display_specific_price -> " + display_specific_price); */
        if (product_specific_price['reduction_type'] != '' && (typeof product_specific_price['reduction_type'] != 'undefined')) {/* || selectedCombination['specific_price'].reduction_type != '')*/

            $('#discount_reduced_price,#old_price').show();
        } else
            $('#discount_reduced_price,#old_price').hide();

        if (product_specific_price['reduction_type'] == 'percentage')/* || selectedCombination['specific_price'].reduction_type == 'percentage')*/
            $('#reduction_percent').show();
        else
            $('#reduction_percent').hide();

    }
    if (display_specific_price)
        $('#not_impacted_by_discount').show();
    else
        $('#not_impacted_by_discount').hide();

    var taxExclPrice = 0;
    /* alert(display_specific_price + " - " + attribut_price_tmp + " - " + currencyRate + " - " + priceTaxExclWithoutGroupReduction);*/
    if (display_specific_price && display_specific_price >= 0)
    {
        if (typeof specific_currency != 'undefined')
            taxExclPrice = display_specific_price + (attribut_price_tmp * currencyRate);
        else
            taxExclPrice = (display_specific_price * currencyRate) + (attribut_price_tmp * currencyRate);
    } else
        taxExclPrice = priceTaxExclWithoutGroupReduction + attribut_price_tmp * currencyRate;

    if (display_specific_price)
        productPriceWithoutReduction = priceTaxExclWithoutGroupReduction;// + selectedCombination['price'] * currencyRate; /* Need to be global => no var*/

    if ((!displayPrice || displayPrice == 2) && !noTaxForThisProduct)
    {
        productPrice = taxExclPrice * tax; /* Need to be global => no var */
        if (display_specific_price)
            productPriceWithoutReduction = ps_round(productPriceWithoutReduction * tax, 2);
    } else
    {
        productPrice = ps_round(taxExclPrice, 2); /* Need to be global => no var */
        if (display_specific_price)
            productPriceWithoutReduction = ps_round(productPriceWithoutReduction, 2);
    }

    var reduction = 0;
    if (typeof product_specific_price != 'undefined') {
        if (product_specific_price.reduction)
        {
            if (product_specific_price.reduction_type == 'amount')
            {
                reduction = (product_specific_price.reduction ? product_specific_price.reduction : product_specific_price.reduction * currencyRate);
                if (displayPrice || noTaxForThisProduct)
                    reduction = ps_round(reduction / tax, 6);
            } else
                reduction = productPrice * (parseFloat(product_specific_price.reduction));
        }
    }
    productPriceWithoutReduction = productPrice * awp_group_reduction;
    productPriceWithoutReduction = ps_round(productPriceWithoutReduction, 2);

    productPrice -= reduction;
    var tmp = productPrice * awp_group_reduction;
    productPrice = ps_round(productPrice * awp_group_reduction, 2);

    var our_price = '';
    if (productPrice > 0) {
        our_price = awpFormatCurrency(productPrice, currencyFormat, currencySign, currencyBlank);
    } else {
        our_price = awpFormatCurrency(0, currencyFormat, currencySign, currencyBlank);
    }
    if (productPriceWithoutReduction > productPrice)
        $('#old_price,#old_price_display,#old_price_display_taxes').show();
    else
        $('#old_price,#old_price_display,#old_price_display_taxes').hide();

    if (!noTaxForThisProduct)
        var productPricePretaxed = productPrice / tax;
    else
        var productPricePretaxed = productPrice;
    var priceProduct = productPrice;
    var productPriceWithoutReduction2 = productPriceWithoutReduction;

    var arr = new Array();
    arr['priceProduct'] = Math.max(priceProduct, 0);
    arr['productPricePretaxed'] = Math.max(productPricePretaxed, 0);
    arr['productPriceWithoutReduction2'] = Math.max(productPriceWithoutReduction2, 0);
    arr['awp_quantity'] = awp_quantity;
    arr['awp_min_quantity'] = awp_min_quantity;
    arr['awp_total_impact'] = awp_total_impact;
    arr['awp_total_weight'] = awp_total_weight;

    /* If the selected combination is connected send the id_combination*/
    if (connected)
        arr['validConnectedCombId'] = idCombOK;
    else
        arr['validConnectedCombId'] = 0;

    return arr;
}

function awp_price_update() {
    var prices = awp_get_total_prices(0,  0);

    var priceProduct = prices['priceProduct'];

    if (typeof defaultConnectedAttribute['price'] != 'undefined')
        defaultConnectedAttributeWithTax = defaultConnectedAttribute['price'];
    else {
        defaultConnectedAttributeWithTax = 0;
        //  defaultConnectedAttribute['price'] = 0;
    }


    if (typeof displayPrice != 'undefined' && displayPrice != 1 && awp_no_tax_impact) {
        defaultConnectedAttributeWithTax = defaultConnectedAttributeWithTax * (1 + (taxRate / 100));

    }
    priceProduct = parseFloat(priceProduct);

    /* If there is a connected combination add price impact */
    if (prices['validConnectedCombId'] > 0) {

        connectedPrice = connectedAttributes[prices['validConnectedCombId']]['price'];
        connectedQty = connectedAttributes[prices['validConnectedCombId']]['quantity'];

        connectedPriceWithTax = connectedAttributes[prices['validConnectedCombId']]['price'];
        if (typeof displayPrice != 'undefined' && displayPrice != 1 && awp_no_tax_impact) {
            connectedPriceWithTax *= 1 + (taxRate / 100);
            /* alert("1.5) "+displayPrice+" =  "+awp_new_price + " / " + taxRate); */
        }

        productPriceWithoutReduction2 = parseFloat(prices['productPriceWithoutReduction2']) + parseFloat(connectedPriceWithTax);

        prices['productPriceWithoutReduction2'] = productPriceWithoutReduction2;
        $('#old_price_display').text(awpFormatCurrency(productPriceWithoutReduction2, currencyFormat, currencySign, currencyBlank));


        if (productPriceWithoutReduction2 > priceProduct)
            $('#old_price,#old_price_display,#old_price_display_taxes').show();
        else
            $('#old_price,#old_price_display,#old_price_display_taxes').hide();

        if (typeof reduction_from != 'undefined' && reduction_from != reduction_to && (currentDate > reduction_to || currentDate < reduction_from))
            var priceReduct = 0;
        else
            var priceReduct = (connectedPrice * (awp_converted_price ? 1 : currencyRate)) / 100 * parseFloat(reduction_percent); // + (reduction_price * currencyRate);

        connectedPrice = connectedPrice - priceReduct;

        if (typeof displayPrice != 'undefined' && displayPrice != 1 && awp_no_tax_impact) {
            connectedPrice *= 1 + (taxRate / 100);
            /* alert("1.5) "+displayPrice+" =  "+awp_new_price + " / " + taxRate); */
        }


        connectedGroupsChar = connectedAttributes[prices['validConnectedCombId']]['id_attribute_groups'];
        totalPricePerCharGroup = new Array();
        impactPerChar = false;
        totalPricePerChar = 0;
        for (i in connectedGroupsChar) {
            idGroupChar = parseInt(connectedGroupsChar[i]);
            totalPricePerCharGroup[idGroupChar] = 0;
            if (typeof awp_groups_chars[idGroupChar] != 'undefined' && awp_groups_chars[idGroupChar]['price_impact_per_char'] == 1) {
                minLimitCharge = awp_groups_chars[idGroupChar]['group_min_limit'];


                if (typeof minLimitCharge == 'undefined' || minLimitCharge == 0 || minLimitCharge < 0) {
                    $minLimitCharge = 1;
                }
                exceptions = awp_groups_chars[idGroupChar]['exceptions'];
                connectedAttributesChar = connectedAttributes[prices['validConnectedCombId']]['attributes_to_groups'][idGroupChar];
                for (k in connectedAttributesChar) {
                    groupType = awp_group_type[idGroupChar];
                    if (groupType == 'textarea')
                        valChars = $('#awp_textarea_group_' + connectedAttributesChar[k]).val();
                    else
                        valChars = $('#awp_textbox_group_' + connectedAttributesChar[k]).val();
                    if (valChars != '') {
                        if (exceptions != '') {
                            exceptionsArr = exceptions.split('');
                            for (i in exceptionsArr) {
                                valChars = valChars.split(exceptionsArr[i]).join('');
                            }
                        }

                        charsValCount = valChars.length;
                        priceImpactPerChar = 0;
                        charsNo = 0;
                        if (minLimitCharge < charsValCount) {
                            charsNo = charsValCount;
                            priceImpactPerChar = charsValCount * parseFloat(connectedPrice);
                        } else {
                            charsValCount = parseFloat(minLimitCharge);
                            priceImpactPerChar = parseFloat(minLimitCharge) * parseFloat(connectedPrice);
                        }
                        totalPricePerCharGroup[idGroupChar] += priceImpactPerChar;
                        totalPricePerChar += priceImpactPerChar;
                        impactPerChar = true;

                    }
                }

            }
        }
        if (impactPerChar) {
            totalPriceChar = 0;
            for (k in totalPricePerCharGroup) {
                if (typeof totalPricePerCharGroup[k] != undefined)
                    totalPriceChar += parseFloat(totalPricePerCharGroup[k]);
            }
            priceProduct = priceProduct + parseFloat(totalPriceChar);
        } else {
            priceProduct = priceProduct + parseFloat(connectedPrice);
        }

    }
    /* End connected attributes */

    var productPricePretaxed = prices['productPricePretaxed'];
    var productPriceWithoutReduction2 = prices['productPriceWithoutReduction2'];
    awp_quantity = prices['awp_quantity'];
    if (prices['validConnectedCombId'] > 0) {
        awp_quantity = connectedQty;
    }
    var awp_total_impact = prices['awp_total_impact'];
    var awp_total_weight = prices['awp_total_weight'];
    var awp_min_quantity = prices['awp_min_quantity'];
    if ($("#quantity_wanted").val() < awp_min_quantity) {
        $("#quantity_wanted").val(awp_min_quantity);
        if ($("#awp_q1").length)
            $("#awp_q1").val(awp_min_quantity);
        if ($("#awp_q2").length)
            $("#awp_q2").val(awp_min_quantity);
    }
    var awp_minimal_text = '';
    if (awp_min_quantity > 1)
        awp_minimal_text = awp_minimal_1 + ' ' + awp_min_quantity + awp_minimal_2;
    /* alert(awp_min_quantity); */

    if (parseFloat(awp_min_quantity) > 1) {
        $('#minimal_quantity_wanted_p').show();
        $('#minimal_quantity_label').html(awp_min_quantity);
    } else {
        $('#minimal_quantity_wanted_p').hide();
        $('#minimal_quantity_label').html(0);
    }


    $('.awp_minimal_text').text(awp_minimal_text);

    var prices_element = $(".product-prices"); // product-prices class is a part of core.js logic, we have small chances it will be changed in customer's theme
    var price_element = prices_element.find("[itemprop='price']").first();
    // Avoid case where meta with price property is picked-up.
    if (!price_element.length || price_element.prop('tagName') == "META") {
        price_element = prices_element.find('.product-price').first();
    }

var discountPriceDisplay = prices_element.find('.product-discount span');
    price_element.text(awpFormatCurrency(priceProduct, currencyFormat, currencySign, currencyBlank))
        .attr("content", priceProduct);
    $(".our_price_display").html(awpFormatCurrency(priceProduct, currencyFormat, currencySign, currencyBlank));
    $('#pretaxe_price_display').text(awpFormatCurrency(productPricePretaxed, currencyFormat, currencySign, currencyBlank));
    $('#old_price_display').text(awpFormatCurrency(productPriceWithoutReduction2, currencyFormat, currencySign, currencyBlank));
discountPriceDisplay.text(awpFormatCurrency(productPriceWithoutReduction2, currencyFormat, currencySign, currencyBlank));

    var availability = $('#product-availability');
    if (availability.length) {
        var availability_icon = availability.find('i').first();
        if (availability_icon.length) {
            availability_icon = availability_icon[0].outerHTML;
        } else {
            availability_icon = '';
        }
        var availability_text = awpAvailableTxt;
        if (awp_quantity == 0 && awp_allow_oosp == '0') {
            availability_text = awpUnaivailableTxt;
        }
        availability.html(availability_icon + availability_text);

        if (awp_quantity == 1) {
            availability.css('display', 'none');
        } else {
            availability.css('display', '');
        }
    }
    $('#quantityAvailable').html(awp_quantity + " ");

    if (awp_stock)
    {
        $('#pQuantityAvailable').fadeIn('slow');
        $('#quantityAvailable').fadeIn('slow');
        $('#awp_in_stock').html($('#product-availability').html());

    }
    $('#awp_p_impact').val(awp_total_impact);
    $('#awp_p_weight').val(awp_total_weight);
    if (document.getElementById('awp_price'))
        $("#awp_price").html($(".our_price_display").html());
    if (document.getElementById('awp_second_price'))
        $("#awp_second_price").html($(".our_price_display").html());

    return prices;
}

function awp_add_to_cart(is_edit)
{
    vars = awp_get_attributes();
    if (vars == -1)
        return;
    if (awp_adc_no_attribute && !vars)
    {
        alert(awp_select_attributes);
        return;
    } else if (!vars)
    {
        vars = new Object();
    }
    prices = awp_price_update();
    vars["price"] = $('#awp_p_impact').val();
    vars["weight"] = $('#awp_p_weight').val();
    vars["quantity"] = ($('#quantity_wanted').length && $('#quantity_wanted').val() > 0 ? $('#quantity_wanted').val() : 1);
    vars["quantity_available"] = $('#quantityAvailable').length ? $('#quantityAvailable').html() : awp_quantity;
    vars["id_product"] = $('#product_page_product_id').length ? $('#product_page_product_id').val() : $(".product_page_product_id:first").val();

    vars["allow_oos"] = allowBuyWhenOutOfStock ? "1" : "";
    if (is_edit)
    {
        vars["awp_ins"] = $('#awp_ins').val();
        vars["awp_ipa"] = $('#awp_ipa').val();
    } else
        $('.awp_edit').hide();

    if (vars["quantity_available"] == 0 && !allowBuyWhenOutOfStock)
    {
        alert(awp_oos_alert);
        return;
    }
    var quantity_group = "";
    /* alert(typeof awp_is_quantity_group); */
    if (awp_is_quantity_group.length > 0)
        for (var qty in awp_is_quantity_group)
        {
            quantity_group = quantity_group + (quantity_group != "" ? "," : "") + awp_is_quantity_group[qty];
            for (var id_att in awp_attr_to_group)
            {
                if (awp_attr_to_group[id_att] == awp_is_quantity_group[qty])
                {
                    if ($('#awp_quantity_group_' + id_att).val() > 0 && $('#awp_quantity_group_' + id_att).val() < prices['awp_min_quantity'])
                    {
                        alert(awp_min_qty_text + ' ' + prices['awp_min_quantity'] + ' (' + $('.qty_name_' + id_att).html() + ')');
                        return false;
                    }
                }
            }
        }
    vars["awp_is_quantity"] = quantity_group;
    $('html, body').animate({scrollTop: 0}, "slow");
    $.ajax({
        type: 'POST',
        url: prestashop.urls.base_url + 'modules/attributewizardpro/combination_json.php',
        async: false,
        cache: false,
        dataType: "json",
        data: vars,
        success: function (feed) {
            if (feed.error != "")
            {
                alert(feed.error);
                return;
            }
            if (awp_ajax)
            {
                id_product = $('#product_page_product_id').length ? $('#product_page_product_id').val() : $('.product_page_product_id').val();

                id_product_attribute = feed.id_product_attribute;
                instructions_valid = feed.added;
                actionURL = prestashop.urls.pages.cart;

                quantity = $('#awp_quantity_wanted_p').val();
                attrSelComb = awp_get_attributes();

                actionData = '';
                for (var k in attrSelComb) {
                    id_group = k.split('_');
                    value = attrSelComb[k];
                    actionData += 'group[' + id_group[1] + ']=' + value + '&';
                }
                actionData += 'token='+$("input[name='token']").val()+'&action=update&id_product_attribute=' + id_product_attribute + '&id_product=' + id_product + '&add=1&qty=' + ((quantity && quantity != null) ? $('#quantity_wanted').val() : '1');
                dataProduct = $('#product-details').attr('data-product');
                window.history.pushState({ id_product_attribute: id_product_attribute }, undefined, '');

                /*$.post(actionURL, actionData, null, 'json').then(function (resp) {
                    if (resp.hasError) {
                        err = '';
                        for (e in resp.errors)
                            err += resp.errors[e];

                        alert(err);
                    } else {*/
                //(idProduct) && (this.instructions_valid == feed.added)
                modalURL = $('.blockcart').attr('data-refresh-url');
                modalData = 'action=add-to-cart&id_product_attribute='+id_product_attribute+'&id_product=' + id_product + '&instructions_valid=' + instructions_valid;;
                dataProduct = $('#product-details').attr('data-product');

                $.post(modalURL, modalData, null, 'json')
                    .done(function (resp) {
                    $('.blockcart').replaceWith(resp.preview);
                    if (resp.modal) {
                    	if(prestashop.blockcart && typeof prestashop.blockcart.showModal === 'function') {
                        	prestashop.blockcart.showModal(resp.modal);
                    	}

                    }
                    dataProduct = $('#product-details').attr('data-product');

                })
                    .done(function () {
                        awp_reset_text();
                        awp_price_update();
                    });
                /*}
            });*/

                if (awp_popup)
                {
                    $("#awp_container").fadeOut(1000);
                    $("#awp_background").fadeOut(1000);
                }
                return;
            } else
            {
                return false;
                /*  if (awp_reload_page == 1)
                      location.reload();
                  else
                      location.href = prestashop.urls.base_url + (awp_psv < 1.5 ? 'order.php' : 'index.php?controller=order');*/
            }
        }
    });


    return false;
}

function awp_customize_func()
{
    showHideDefaultElementsAWP();

    awp_max_gi = 0;
    $('.awp_gi').each(function () {
        if ($(this).width() > awp_max_gi)
            awp_max_gi = $(this).width();
    });
    if (awp_max_gi > 0)
        $('.awp_box_inner').width($('.awp_content').width() - awp_max_gi - 18);

}

function awp_add_to_cart_func()
{
    if ($('#awp_add_to_cart input').val() != awp_add_cart)
    {
        $('#awp_add_to_cart input').val(awp_add_cart);
        $('#awp_add_to_cart input').unbind('click').click(function () {
            awp_add_to_cart();
            return false;
        });
    }

}


function awp_do_customize()
{
    if (awp_customize !== awp_a2c_element.text())
    {
        // replace Customize text with Add to Cart
        awp_a2c_element.html(awp_a2c_element.html().replace(awp_a2c_element.text(), awp_customize));
    }

    awp_add_to_cart_button.show();
    awp_add_to_cart_button.unbind('click').click(function () {
        awp_do_popup();
        return false;
    });

}

function awp_do_popup()
{
    // console.log('awp_do_popup');
    $("#awp_background").fadeIn(1000);
    $("#awp_container").fadeIn(1000);

    scrollToElement("body", 400); // TODO check negative vertical offset

    awp_max_gi = 0;

    $('.awp_gi').each(function () {
        if ($(this).width() > awp_max_gi)
            awp_max_gi = $(this).width();
    });
    if (awp_max_gi > 0)
        $('.awp_box_inner').width($('.awp_content').width() - awp_max_gi - 18);

    if ($('#awp_add_to_cart a').length)
        awp_add_to_cart_button = $('#awp_add_to_cart a');
    else if ($('#awp_add_to_cart button').length)
        awp_add_to_cart_button = $('#awp_add_to_cart button');
    else
        awp_add_to_cart_button = $('#awp_add_to_cart input');
    if ($('#awp_add_to_cart a').length || $('#awp_add_to_cart button').length)
        awp_add_to_cart_button.find('span').html(awp_add_cart);
    else
        awp_add_to_cart_button.val(awp_add_cart);
    awp_add_to_cart_button.unbind('click').click(function () {
        awp_add_to_cart_button.attr('disabled', true);
        awp_add_to_cart();
        awp_customize_func();
        $('#awp_add_to_cart input').attr('disabled', false);
        return false;
    });

}

function in_array2(arr, obj)
{
    for (var i = 0; i < arr.length; i++)
    {
        if (arr[i] == obj)
        {
            return true;
        }
    }
}

function awp_get_attributes()
{
    awp_required_check = new Array();
    var name = document.awp_wizard;
    var vars = new Object();
    var added = false;
    for (var id_group in awp_chk_limit)
    {
        total_checked = $('.awp_cell_cont_' + id_group + ' input[type=checkbox]:checked').length;

        if (awp_chk_limit[id_group][0] > 0 && awp_chk_limit[id_group][0] == awp_chk_limit[id_group][1] && awp_chk_limit[id_group][0] != total_checked)
        {
            alert(awp_must_select + ' ' + awp_chk_limit[id_group][0] + ' ' + awp_group_name[id_group] + ' ' + (awp_chk_limit[id_group][0] == 1 ? awp_option : awp_options));
            return -1;
        } else if (awp_chk_limit[id_group][0] > 0 && awp_chk_limit[id_group][1] > 0 && (awp_chk_limit[id_group][0] > total_checked || awp_chk_limit[id_group][1] < total_checked))
        {
            alert(awp_must_select + ' ' + awp_chk_limit[id_group][0] + ' - ' + awp_chk_limit[id_group][1] + ' ' + awp_group_name[id_group] + ' ' + awp_options);
            return -1;
        } else if (awp_chk_limit[id_group][0] > 0 && awp_chk_limit[id_group][1] <= 0 && awp_chk_limit[id_group][0] > total_checked)
        {
            alert(awp_must_select_least + ' ' + awp_chk_limit[id_group][0] + ' ' + awp_group_name[id_group] + ' ' + (awp_chk_limit[id_group][0] == 1 ? awp_option : awp_options));
            return -1;
        } else if (awp_chk_limit[id_group][1] > 0 && awp_chk_limit[id_group][0] <= 0 && awp_chk_limit[id_group][1] < total_checked)
        {
            alert(awp_must_select_up + ' ' + awp_chk_limit[id_group][1] + ' ' + awp_group_name[id_group] + ' ' + (awp_chk_limit[id_group][1] == 1 ? awp_option : awp_options));
            return -1;
        }
    }
    for (i = 0; i < name.elements.length; i++)
    {
        if (name.elements[i].name.substring(0, 10) == "awp_group_" &&
            (name.elements[i].type != "checkbox" || name.elements[i].checked) &&
            name.elements[i].name != "")
        {
            var tmp_arr = name.elements[i].name.substring(4).split("_");
            if (tmp_arr.length == 3)
            {
                var found = false;
                for (var key in awp_required_list)
                {
                    if (key == tmp_arr[2] && awp_required_list[key] == 1)
                        found = true;
                }
                if (found && name.elements[i].value == "")
                {
                    alert(Encoder.htmlDecode(awp_required_list_name[tmp_arr[2]]) + " " + awp_is_required);
                    name.elements[i].focus();
                    return -1;
                } else if (name.elements[i].value == "")
                    continue;
                if (in_array2(awp_is_quantity_group, tmp_arr[1]) && name.elements[i].value == 0)
                    continue;
            }
            if (tmp_arr.length == 2 && awp_required_group[tmp_arr[1]])
            {
                if (name.elements[i].type == 'select-one' && name.elements[i].value == '')
                {
                    alert(Encoder.htmlDecode(awp_group_name[tmp_arr[1]]) + " " + awp_is_required);
                    return -1;
                } else if (name.elements[i].type == 'radio')
                {
                    if (tmp_arr[1] in awp_required_check)
                    {
                        if (awp_required_check[tmp_arr[1]] != true)
                            awp_required_check[tmp_arr[1]] = name.elements[i].checked;
                    } else
                        awp_required_check[tmp_arr[1]] = name.elements[i].checked;
                }
            }
            if ((name.elements[i].type != "radio" || name.elements[i].checked))
            {
                if (vars[name.elements[i].name.substring(4)])
                    vars[encodeURI(name.elements[i].name.substring(4) + "_" + i)] = encodeURIComponent(name.elements[i].value);
                else
                    vars[encodeURI(name.elements[i].name.substring(4))] = encodeURIComponent(name.elements[i].value);
                added = true;
            }
        }
    }
    for (var key in awp_required_check)
    {
        if (!awp_required_check[key])
        {
            alert(Encoder.htmlDecode(awp_group_name[key]) + " " + awp_is_required);
            return -1;
        }
    }
    return added ? vars : false;
}

function awp_reset_text()
{
    var name = document.awp_wizard;
    for (i = 0; i < name.elements.length; i++)
        if (name.elements[i].name.substring(0, 10) == "awp_group_" &&
            (name.elements[i].type == "text" || name.elements[i].type == "textarea" || name.elements[i].type == "hidden"))
        {
            if (name.elements[i].type == "hidden")
            {
                var img_arr = name.elements[i].value.split("_");
                $("#awp_image_cell_" + img_arr[1]).css('display', 'none');
                $("#awp_image_delete_cell_" + img_arr[1]).css('display', 'none');
                name.elements[i].value = "";
            } else
            {
                var group_arr = name.elements[i].name.split("_");
                if (awp_group_type[group_arr[2]] == "quantity")
                    var foo = "do_nothing";/*name.elements[i].value = "0"; */
                else if (awp_group_type[group_arr[2]] == "calculation")
                    name.elements[i].value = $('#awp_calc_group_' + group_arr[3]).attr('default');
                else
                    name.elements[i].value = "";
            }
        }
    $('.awp_max_limit').each(function () {
        $(this).html($(this).attr('awp_limit'));
    });
    $('.up_image_hide').hide();
    $('.up_image_clear').html('');

}

function awp_max_limit_check(id_attribute, max_limit)
{
    var obj_id = $('#awp_textbox_group_' + id_attribute).length ? '#awp_textbox_group_' + id_attribute : '#awp_textarea_group_' + id_attribute;
    var chars_left = max_limit - $(obj_id).val().length;
    $('#awp_max_limit_' + id_attribute).html(Math.max(chars_left, 0).toString());
    if (chars_left <= 0)
        $(obj_id).val($(obj_id).val().substring(0, max_limit));
}

function awp_toggle_img(awp_group, awp_att)
{
    $(".awp_gi_" + awp_group).each(function () {
        var awp_cur_att = $(this).attr('id').substring(7);
        if ($('#awp_radio_group_' + awp_cur_att).attr('checked') == 'checked' || $('#awp_radio_group_' + awp_cur_att).attr('checked') == true)
        {
            $('#awp_tc_' + awp_cur_att).addClass('awp_image_sel');
            $('#awp_tc_' + awp_cur_att).removeClass('awp_image_nosel');
        } else
        {
            $('#awp_tc_' + awp_cur_att).addClass('awp_image_nosel');
            $('#awp_tc_' + awp_cur_att).removeClass('awp_image_sel');
        }
    });
}

function awp_center_images(id_group)
{
//console.log("Starting awp_center_images group " + id_group);
    if (!awp_isQuickView)
        if (typeof awp_center_images_done[id_group] != 'undefined')
            return;
        else
            awp_center_images_done[id_group] = true;

    awp_mcw = 0;
    awp_pop_display = $('#awp_container').css('display');
//console.log("display ="+awp_pop_display);
    if (awp_popup && awp_pop_display == 'none')
        $('#awp_container').show();
    $(".awp_cell_cont_" + id_group).css('width', '');
    $(".awp_cell_cont_" + id_group).each(function () {
//    if (id_group == 16)
//    console.log($(this).width());
        awp_mcw = Math.max(awp_mcw, $(this).width() + 20);
    });

    $(".awp_cell_cont_" + id_group).width(awp_mcw);
//console.log("awp_cell_cont_ = " + awp_mcw);
    imgWidth = $('.awp_cell_cont_' + id_group + ' .awp_group_image').width();
    if ($("#awp_group_layout_"+id_group).val() == 0) {
        if (awp_mcw - 22 !== imgWidth)
        $('.awp_cell_cont_' + id_group + ' .awp_full_text').width(awp_mcw - imgWidth - 15);
    }
    

    if (awp_popup && awp_pop_display == 'none')
        $('#awp_container').hide();
}

function scrollToElement(selector, time, verticalOffset)
{
    time = typeof (o) != 'undefined' ? time : 1000;
    verticalOffset = typeof (verticalOffset) != 'undefined' ? verticalOffset : 0;
    element = $(selector);
    offset = element.offset();
    offsetTop = offset.top + verticalOffset;
    $('html, body').animate({
        scrollTop: offsetTop
    }, time);
}

function applyFileUpload()
{
    for (var n = 0; n < awp_file_list.length; n++)
    {
        var i = awp_file_list[n];
        new AjaxUpload('#upload_button_' + i, i, awp_file_ext[n], {
            action: prestashop.urls.base_url + 'modules/attributewizardpro/file_upload.php',
            name: 'userfile',
            data: {id_product: $('#product_page_product_id').val(), id_attribute: i},
            /* Submit file after selection */
            autoSubmit: true,
            responseType: false,
            onSubmit: function (file, ext, i, allowed_ext)
            {
                if (!(ext && allowed_ext.test(ext)))
                {
                    alert(awp_ext_err + " " + allowed_ext.source.substring(2, allowed_ext.source.length - 2).replace(/\|/g, ", "));
                    return false;
                }
                $('#awp_image_cell_' + i).html('<img src="' + prestashop.urls.base_url + 'modules/attributewizardpro/views/img/loading.gif" /><br /><b>Please wait</b>');
            },
            onComplete: function (file, ext, response, i)
            {
                if (response.indexOf('|||') == -1)
                {
                    alert(response);
                    $('#awp_image_cell_' + i).html('');
                    $('#awp_image_delete_cell_' + i).css('display', 'none');
                    $('#awp_file_group_' + i).val('');
                    awp_price_update();
                    return;
                }
                var response_arr = response.split("|||", 2);
                var no_thumb = false;
                if (response_arr[1].substring(0, 1) == '|')
                {
                    response_arr[1] = response_arr[1].substring(1);
                    no_thumb = true;
                }
                if (!(ext && /^(jpg|png|jpeg|gif)$/.test(ext)) || no_thumb)
                    $('#awp_image_cell_' + i).html(response_arr[1]);
                else
                {
                    var thumb = response_arr[0].substr(0, response_arr[0].length - ext.length - 3) + "_small.jpg";
                    $('#awp_image_cell_' + i).html('<img src="' + prestashop.urls.base_url + 'modules/attributewizardpro/file_uploads/' + thumb + '" />');
                    $('#awp_image_cell_' + i).css('display', 'block');
                }
                $('#awp_image_delete_cell_' + i).css('display', 'block');
                $('#awp_file_group_' + i).val(response.replace('||||', '|||'));
                awp_price_update();
            }
        });
    }
}

function removeClearBoth()
{

    $('.awp_box  .awp_content').each(function(){
        boxContent = $(this);
        cellCont = boxContent.find('.awp_cell_cont');
        cellClear = boxContent.find('.awp_clear');

        attributesPerRow = cellCont.length / cellClear.length;

        widthCellCont = attributesPerRow * cellCont.width();


        if (boxContent.width() < widthCellCont) {
            cellClear.removeClass('awp_clear');
        }
    });
}
$(document).ready(function () {
    var isTemplateReady = !!$('#awp_wizard').length;
    if(!isTemplateReady) {
        return;
    }

    var global_validComb; // dirty solution to execute code inside awp_select+first only once
    showHideDefaultElementsAWP();

    setTimeout(function () {
        removeClearBoth();
    }, 700);

    if (awp_collapse_block) {
        $(".awp_header_collapible").click(function () {
            var $awp_box = $(this).parent().parent();
            $awp_box.find('.awp_description').toggle("slow");

            var $awp_content = $awp_box.find('.awp_content');

            $awp_content.toggle("slow", function () {

                if ($awp_content.is(':visible')) {
                    $awp_box.find('.awp_arrow_up').show();
                    $awp_box.find('.awp_arrow_down').hide();
                } else {
                    $awp_box.find('.awp_arrow_up').hide();
                    $awp_box.find('.awp_arrow_down').show();
                }
            });

        });

        $('.awp_header_collapible').each(function (index, elem) {

            var $awp_box = $(elem).parent().parent();

            if (index != 0) {
                $awp_box.find('.awp_description').toggle("slow");
                $awp_box.find('.awp_content').toggle("slow");

                $awp_box.find('.awp_arrow_up').hide();
                $awp_box.find('.awp_arrow_down').show();

            } else {

                $awp_box.find('.awp_arrow_up').show();
                $awp_box.find('.awp_arrow_down').hide();
            }
        });

    }
    $(window).unbind('hashchange').bind('hashchange', function () {
        checkUrl();
    });

    if (awp_popup)
    {
        if (awp_is_edit)
        {
            awp_do_popup();
        } else
        {
            awp_do_customize();
        }
    }



    if (awp_is_quantity_group.length > 0)
    {
        $("#quantity_wanted_p").css('display', 'none');
        $("div.awp_quantity_additional").css('display', 'none');
    } else
    {
        $("#quantity_wanted_p").css('display', 'block');
    }
    $('#quantity_wanted').keyup(function () {
        if ($('#awp_q1').length)
            $('#awp_q1').val($('#quantity_wanted').val());
        if ($('#awp_q2').length)
            $('#awp_q2').val($('#quantity_wanted').val());
    });
    if (awp_is_edit)
    {
        if (awp_qty_edit > 0)
        {
            $('#quantity_wanted').val(awp_qty_edit);
            if ($('#awp_q1').length)
                $('#awp_q1').val($('#quantity_wanted').val());
            if ($('#awp_q2').length)
                $('#awp_q2').val($('#quantity_wanted').val());
        }
        if (!$('#awp_edit').length)
        {
            $('#awp_add_to_cart').before('<p class="buttons_bottom_block" id="awp_edit"><input type="button" class="exclusive awp_edit" value="' + awp_edit + '" name="Submit"  /></p>');
        }
    }
$('#awp_edit').click(function(){
    $(this).prop('disabled', true);
    $('.awp_edit').fadeOut();
    awp_add_to_cart(true);
});

    if ((awp_add_to_cart_display == "both" || awp_add_to_cart_display == "bottom") && document.getElementById('awp_price'))
    {
        $("#awp_price").html($("#our_price_display").html())
        $("#awp_second_price").html($("#our_price_display").html());
    }
    if ($(window).width() >= 768 && (awp_add_to_cart_display == "both" || awp_add_to_cart_display == "scroll"))
    {
        $('.product-add-to-cart').css('position', 'absolute');
        $('.product-add-to-cart').css('z-index', '20001');
        $('.product-additional-info').css('padding-top' , $('.product-add-to-cart').height());
        var $scrollingDiv = $(".product-add-to-cart");
        $(window).scroll(function () {
            $scrollingDiv
                .stop()
                .animate({"marginTop": (Math.max(0, $(window).scrollTop() - 250)) + "px", "marginLeft":'0'+ "px"}, "slow");
        });
    }
    for (var id_attribute in awp_impact_list)
    {
        var tmp_impact = parseFloat(awp_impact_list[id_attribute]);
        if (!awp_attr_to_group[id_attribute])
            continue;
        var tmp_group = awp_attr_to_group[id_attribute];
        if (tmp_impact != 0)
            awp_group_impact[tmp_group] = 1;
        else if (awp_group_impact[tmp_group] != 1)
            awp_group_impact[tmp_group] = 0;
    }

    setTimeout(function () {
        applyFileUpload();
        setTimeout(function () {
            applyFileUpload();
        }, 6000);
    }, 3000);

    if (typeof attribute_anchor_separator == 'undefined')
        attribute_anchor_separator = '-';


});


function isValid(value) {
    if(value === null || typeof  value === "undefined") {
        return false;
    }
    return true;
}

function setFavicon() {
    var link = $('link[type="image/x-icon"]').remove().attr("href");
    $('<link href="' + link + '" rel="shortcut icon" type="image/x-icon" />').appendTo('head');
}


$(document).ready(function () {
    var isTemplateReady = !!$('#awp_wizard').length;
    if(!isTemplateReady) {
        return;
    }
    
    $('.fancybox.shown').click(function(e){
        e.preventDefault();
        awpImgModal = $(this).attr('href');
                
        awpModal = $('#awpModal');
        
        awpModal.on('show.bs.modal', function (event) {
            var modal = $(this);
            modal.find('.modal-body').html('<img style="width: 100%" src="' + awpImgModal + '"/>');
        });
        
        awpModal.modal();
    });

    $('#quantityAvailable').css('display', 'none');
    $('#quantityAvailableTxt').css('display', 'none');
    $('#quantityAvailableTxtMultiple').css('display', 'none');
    $('#last_quantities').css('display', 'none');

    if (awp_add_to_cart_display == "bottom")
    {
        $("#quantity_wanted_p").attr("id", "awp_quantity_wanted_p");
        $("#awp_quantity_wanted_p").css("display", "none");
    }

    // popup pre-set
    var body = $('body');
    awp_popup_style();
    if (awp_fade)
    {
        body.prepend('<div id="awp_background"  class="awp_fade"></div>');
    }
    body.on("click", "#awp_container .close, #awp_background", function () {
        $("#awp_container, #awp_background").fadeOut(1000);
        awp_customize_func();
    });

    var selectedDefAttribute = {};
    $.each(awp_groups, function (index, id_group) {
        //setTimeout(function(){
//            awp_center_images(id_group);
        //}, 500);

        $('.awp_box .awp_cell_cont_' + id_group + ' .awp_attribute_selected').each(function () {
            this_awp_attribute = $(this);

            awp_selected_attribute = $(this).val();

            if ((this_awp_attribute.attr('type') != 'radio' || this_awp_attribute.attr('checked')) &&
                (this_awp_attribute.attr('type') != 'checkbox' || this_awp_attribute.attr('checked')) &&
                (this_awp_attribute.attr('type') != 'text' || this_awp_attribute.val() != "0") && this_awp_attribute.val() != "")
            {
                awp_tmp_arr = this_awp_attribute.attr('name').split('_');
                if (awp_selected_group == awp_tmp_arr[2])
                {
                    if (awp_group_type[awp_tmp_arr[2]] != "quantity" && awp_tmp_arr.length == 4 && awp_tmp_arr[3] != awp_selected_attribute)
                        awp_selected_attribute = awp_tmp_arr[3];
                    else if (awp_group_type[awp_tmp_arr[2]] != "quantity" && awp_group_type[awp_tmp_arr[2]] != "textbox" &&
                        awp_group_type[awp_tmp_arr[2]] != "textarea" && awp_group_type[awp_tmp_arr[2]] != "file" &&
                        awp_group_type[awp_tmp_arr[2]] != "calculation" && awp_selected_attribute != this_awp_attribute.val())
                        awp_selected_attribute = this_awp_attribute.val();
                }
            }
            if ((this_awp_attribute.attr('type') == 'radio' || this_awp_attribute.attr('checked'))) {
                awp_selected_attribute = this_awp_attribute.filter(':checked').val();
            }
            if (awp_selected_attribute != '') {
                awp_select(id_group, awp_selected_attribute, awp_currency, true);
                selectedDefAttribute['id_group'] = id_group;
                selectedDefAttribute['id_attribute'] = awp_selected_attribute;
            } 
        });
    });
    if ($.isEmptyObject(selectedDefAttribute)) {
        // if no default selected
        // todo: why?
        for (id_group in defaultConnectedAttribute['attributes_to_groups']) {
            for (i in defaultConnectedAttribute['attributes_to_groups'][id_group])
                awp_select(id_group,defaultConnectedAttribute['attributes_to_groups'][id_group][i], true );
        }
    }

    // show the product thumbnails after all awp_select calls are done
    if (typeof global_validComb !== 'undefined')
        show_cover_thumbnails(global_validComb);

    setTimeout(function(){
       awp_resize_blocks();
     }, 2500);

    if (awp_layered_image_list.length > 0)
        $('.zoom-in').css('display', 'none');

});

function awp_resize_blocks() {
    $.each(awp_sel_cont_var, function (id_attribute, group_height) {
        if ((group_height) && group_height != 0) {
            $("#awp_sel_cont_" + id_attribute).css('margin-top', (group_height / 2) - 8);
            $("#awp_radio_cell" + id_attribute).css('margin-top', (group_height / 2) - 8);
            $("#awp_impact_cell" + id_attribute).css('margin-top', (group_height / 2) - 8);
            $("#awp_textbox_cell" + id_attribute).css('margin-top', (group_height / 2) - 8);
            $("#awp_impact_cell" + id_attribute).css('margin-top', (group_height / 2) - 8);
            $("#awp_text_length_" + id_attribute).css('margin-top', (group_height / 2) - 8);

            $("#awp_impact_cell" + id_attribute).css('margin-top', (group_height / 2) - 8);
            $("#awp_text_length_" + id_attribute).css('margin-top', (group_height / 2) - 8);

            $("#awp_textbox_cell" + id_attribute).css('margin-top', (group_height / 2) - 8);
            $("#awp_file_cell" + id_attribute).css('margin-top', (group_height / 2) - 8);
            $("#awp_image_delete_cell_" + id_attribute).css('margin-top', (group_height / 2) - 8);
            $("#awp_impact_cell" + id_attribute).css('margin-top', (group_height / 2) - 8);
            $("#awp_text_length_" + id_attribute).css('margin-top', (group_height / 2) - 8);

            $("#awp_checkbox_cell" + id_attribute).css('margin-top', (group_height / 2) - 8);
            $("#awp_impact_cell" + id_attribute).css('margin-top', (group_height / 2) - 8);


            $("#awp_quantity_cell" + id_attribute).css('margin-top', (group_height / 2) - 8);
            $("#awp_impact_cell" + id_attribute).css('margin-top', (group_height / 2) - 8);
        }
    });
//console.log("Starting awp_center_images");
    $.each(awp_groups, function (index, id_group) {
            awp_center_images(id_group);
    });
//console.log("Finished awp_center_images");

    $.each(awp_cell_cont_text_group, function (id_attribute, id_group) {

        if (typeof id_group != 'undefined') {
            awp_max_text_length[id_group] = 0;
            $('.awp_cell_cont_' + id_group + ' .awp_text_length_group').each(function () {
                awp_max_text_length[id_group] = Math.max(awp_max_text_length[id_group], $(this).width());
            });
//console.log("awp_resize_blocks Center images group " + id_group);
           awp_center_images(id_group);
        }
    });


    $.each(awp_max_text_length, function ( id_group, width) {
        if (width > 0)
             $('.awp_cell_cont_' + id_group + ' .awp_text_length_group').width(width);
    });
    awp_max_gi = 0;
    $('.awp_gi').each(function () {
        if ($(this).width() > awp_max_gi)
            awp_max_gi = $(this).width();
    });
    if (awp_max_gi > 0)
    {
        $('.awp_box_inner').width($('.awp_content').width() - awp_max_gi - 18);
        $(window).resize(function () {
            $('.awp_box_inner').width($('.awp_content').width() - awp_max_gi - 18);
        });
    }
}

function awp_popup_style() {
    // If pop-up is already rendered, following operations are redundant and causes text-box glitch.
    if (!awp_popup || $('#awp_container').is(":visible")) {
        return false;
    }
    var awp_container = $("#awp_container"),
        awp_background = $("#awp_background"),
        content_wrapper = ($("#center-column").length) ? $("#center-column") : $("#content-wrapper"); // #center-column
    // content_wrapper_width = awp_container.parent().width();

    // wrapper = $("#page").length ? $("#page") : $("#wrapper"); // #page
    wrapper = $("body");
    awp_container.detach().prependTo(wrapper);

    var popup_style = {
        "top": awp_popup_top + 'px',
        "width": (content_wrapper.length) ? content_wrapper.width() * 0.92 + 'px' : awp_popup_width, // NOTICE now customer defined value hidden on config page
        "margin-left": (wrapper.width() - content_wrapper.width()*0.92)/2 + 'px'
    };
    var fade_style = {
        height: Math.max(wrapper.height(), document.documentElement.getBoundingClientRect().height, document.body.scrollHeight),
        width: Math.max(wrapper.width(), $(window).width()),
        filter: "alpha(opacity=" + awp_opacity + ")",
        opacity: awp_opacity / 100 // awp_opacity_fraction
    };

    $.each(popup_style, function (property, value) {
        awp_container.css(property, value);
    });
    $.each(fade_style, function (property, value) {
        awp_background.css(property, value);
    });
}
$(window).resize(awp_popup_style);

/*
* new format function uses currency chars/separators prepared on backend
* see awp_currency_chars in global scope,
* @example awp_currency_chars = [
  "sign" => " $CA"
  "sign_position" => "end"
  "thousands" => " "
  "decimal" => ","
* ];
 */
function awpFormatCurrency(price) {
    price = ps_round(price, priceDisplayPrecision).toFixed(priceDisplayPrecision).toString();

    var price_parts = price.match(/(\d+)(\.)(\d+)/); // index 1 - thousands, 2 - separator, 3 - decimals

    // if local format has thousands separator, we add it in the price thousands chunks
    if (awp_currency_chars.thousands !== null && typeof awp_currency_chars.thousands === 'object') {
        price_parts[1] = price_parts[1].split('').reverse().join('')
            .match(/\d{1,3}/).reverse().join(awp_currency_chars.thousands);
    }

    price_parts[2] = awp_currency_chars.decimal;
    if ('start' === awp_currency_chars.sign_position) {
        price_parts[0] = awp_currency_chars.sign;
    } else {
        price_parts[4] = awp_currency_chars.sign;
        price_parts.splice(0, 1); // delete first element since it contains whole matched string
    }

    price = price_parts.join('');

    return price;
}

function formatCurrencyOld(price, currencyFormat, currencySign, currencyBlank)
{
    // if you modified this function, don't forget to modify the PHP function displayPrice (in the Tools.php class)
    var blank = '';
    price = parseFloat(price.toFixed(10));
    price = ps_round(price, priceDisplayPrecision);
    if (currencyBlank > 0)
        blank = ' ';

    var posCurrency = currencyFormat.indexOf("\u00a0\u00a4");
    if (posCurrency == -1) {
        var posCurrency = currencyFormat.indexOf("\u00a4");
    } else {
        currencySign = " " + currencySign;
    }

    currencyFormat = currencyFormat.replace(new RegExp('\u00a0', 'g'), '');
    currencyFormat = currencyFormat.replace(new RegExp('¤', 'g'), '');
    currencyFormat = currencyFormat.replace(new RegExp('\u00a4', 'g'), '');
    currencyFormat = currencyFormat.replace(new RegExp('#', 'g'), '');
    currencyFormat = currencyFormat.replace(new RegExp('0', 'g'), '');
    currencyFormat = currencyFormat.replace(new RegExp(' ', 'g'), '');

    thousand = currencyFormat.substr(0, 1);
    decimal = currencyFormat.substr(1, 1);
    return price.formatMoney(priceDisplayPrecision, currencySign, thousand, decimal, posCurrency);
}

function ps_log10(value) {
    return Math.log(value) / Math.LN10;
}

function ps_round(value, places)
{
    if (typeof (roundMode) === 'undefined')
        roundMode = 2;
    if (typeof (places) === 'undefined')
        places = 2;

    var method = roundMode;

    if (method === 0)
        return ceilf(value, places);
    else if (method === 1)
        return floorf(value, places);
    else if (method === 2)
        return ps_round_half_up(value, places);
    else if (method == 3 || method == 4 || method == 5)
    {
        // From PHP Math.c
        var precision_places = 14 - Math.floor(ps_log10(Math.abs(value)));
        var f1 = Math.pow(10, Math.abs(places));

        if (precision_places > places && precision_places - places < 15)
        {
            var f2 = Math.pow(10, Math.abs(precision_places));
            if (precision_places >= 0)
                tmp_value = value * f2;
            else
                tmp_value = value / f2;

            tmp_value = ps_round_helper(tmp_value, roundMode);

            /* now correctly move the decimal point */
            f2 = Math.pow(10, Math.abs(places - precision_places));
            /* because places < precision_places */
            tmp_value /= f2;
        } else
        {
            /* adjust the value */
            if (places >= 0)
                tmp_value = value * f1;
            else
                tmp_value = value / f1;

            if (Math.abs(tmp_value) >= 1e15)
                return value;
        }

        tmp_value = ps_round_helper(tmp_value, roundMode);
        if (places > 0)
            tmp_value = tmp_value / f1;
        else
            tmp_value = tmp_value * f1;

        return tmp_value;
    }
}
// todo get rig of this 1.6 logic
function ceilf(value, precision)
{
    if (typeof(precision) === 'undefined')
        precision = 0;
    var precisionFactor = precision === 0 ? 1 : Math.pow(10, precision);
    var tmp = value * precisionFactor;
    var tmp2 = tmp.toString();
    if (tmp2[tmp2.length - 1] === 0)
        return value;
    return Math.ceil(value * precisionFactor) / precisionFactor;
}

function floorf(value, precision)
{
    if (typeof(precision) === 'undefined')
        precision = 0;
    var precisionFactor = precision === 0 ? 1 : Math.pow(10, precision);
    var tmp = value * precisionFactor;
    var tmp2 = tmp.toString();
    if (tmp2[tmp2.length - 1] === 0)
        return value;
    return Math.floor(value * precisionFactor) / precisionFactor;
}
function ps_round_helper(value, mode)
{
    // From PHP Math.c
    if (value >= 0.0)
    {
        tmp_value = Math.floor(value + 0.5);
        if ((mode == 3 && value == (-0.5 + tmp_value)) ||
            (mode == 4 && value == (0.5 + 2 * Math.floor(tmp_value / 2.0))) ||
            (mode == 5 && value == (0.5 + 2 * Math.floor(tmp_value / 2.0) - 1.0)))
            tmp_value -= 1.0;
    } else
    {
        tmp_value = Math.ceil(value - 0.5);
        if ((mode == 3 && value == (0.5 + tmp_value)) ||
            (mode == 4 && value == (-0.5 + 2 * Math.ceil(tmp_value / 2.0))) ||
            (mode == 5 && value == (-0.5 + 2 * Math.ceil(tmp_value / 2.0) + 1.0)))
            tmp_value += 1.0;
    }

    return tmp_value;
}

function ps_round_half_up(value, precision)
{
    var mul = Math.pow(10, precision);
    var val = value * mul;

    var next_digit = Math.floor(val * 10) - 10 * Math.floor(val);
    if (next_digit >= 5)
        val = Math.ceil(val);
    else
        val = Math.floor(val);

    return val / mul;
}


/* PS 1.7 changes*/

function showHideDefaultElementsAWP(no_customize) {

    if ($('#awp_container').length < 0) {
        return;
    }

    if(undefined === no_customize) {
        no_customize = false;
    }

    setTimeout(function () {
        awp_popup_style();
    }, 100);

    $(".product-variants").css('display', 'none');

    $('.product-add-to-cart .add .add-to-cart, .product-add-to-cart .add-to-cart').attr({id: 'awp_add_to_cart'}).removeAttr('data-button-action');
    // #awp_add_to_cart exists from here
    //set Add to cart button values in global context
    awp_add_to_cart_button = $('#awp_add_to_cart');
    awp_add_to_cart_button.attr('type', 'button').fadeIn(600);

    var awp_icon = awp_add_to_cart_button.find("i").clone().wrap('<p>').parent().html() || ''; // get customer's theme button's icon

    awp_add_to_cart_button.removeAttr('disabled');
    if (typeof awp_no_customize == 'undefined')
        awp_no_customize = false;

    awp_a2c_element = awp_add_to_cart_button;
    //Get a text of the button itself. //clone the element, //select all the children,  //remove all the children, //again go back to selected element
    if (awp_a2c_original_text == undefined)
        awp_a2c_original_text = awp_add_to_cart_button.clone().children().remove().end().text();
    var add2cart_children = awp_add_to_cart_button.find("button,span,a").get().reverse();
    // we get text of last not empty children element as original text
    $.each(add2cart_children, function () {
        if ($(this).text()) {
            awp_a2c_element = $(this);
            awp_a2c_original_text = $(this).text();
            return true;
        }
    });

    awp_no_customize = parseInt(awp_no_customize);
    if (!awp_no_customize && !no_customize) {
        if (awp_add_to_cart_button.length)
            awp_add_to_cart_button.html(awp_icon + awp_customize);
    }


    $('#awp_add_to_cart, body .quickshop_wraper p#awp_add_to_cart input').unbind('click').click(function () {
        // to avoid scroll when button already has "Add" text. // todo refactor it!
        var awp_a2c_current_text = awp_add_to_cart_button.clone().children().remove().end().text();

        if (awp_popup) {
            awp_do_popup();

            $("#header").css('z-index', '0');
        } else {
            if (!awp_no_customize && awp_customize === awp_a2c_current_text) {

                scrollToElement('#awp_container', 1200);

            }
        }
        if (!awp_no_customize && awp_customize === awp_a2c_current_text) {
            if (awp_add_to_cart_button.length) {
                awp_add_to_cart_button.html(awp_icon + awp_a2c_original_text);
                awp_add_to_cart_button.unbind('click').click(function () {
                    awp_add_to_cart();
                    return false;
                });
                return false;
            }

        } else {
            awp_add_to_cart();
            return false;
        }
    });

    if (typeof awp_popup == 'undefined')
        awp_popup = false;

    // Disabled, causes AWP pop-up to disappear after combination change is triggered.
    // if (awp_popup) {
    //     $("#awp_container").css('display', 'none');
    // }


    if (awp_add_to_cart_display == "bottom") {
        $(".qty").attr("class", "awp_qty");
        $(".awp_qty").css("display", "none");
        $(".product-add-to-cart span.control-label").css("display", "none");

    }
    if (typeof PS_CATALOG_MODE == 'undefined')
        PS_CATALOG_MODE = false;
    if (PS_CATALOG_MODE)
        $('#awp_add_to_cart, #addp_to_cart_quickshop').css("display", "none");
    if (typeof productAvailableForOrder == 'undefined')
        productAvailableForOrder = 1;
    if (productAvailableForOrder == 0) {
        $('#awp_add_to_cart input, #add_to_cart_quickshop input').css("display", "none");

        if ($awp_add_to_cart == "both" || $awp_add_to_cart == "bottom") {
            $('#awp_footer_add_to_cart').css("display", "none");
            $('.awp_quantity_additional').css("display", "none");

        }

        if (($awp_add_to_cart == "both" || $awp_add_to_cart == "bottom") && count($awp_groups) >= $awp_second_add) {
            $('#awp_top_add_to_cart').css("display", "none");
            $('.awp_quantity_additional').css("display", "none");
        }

    }
}

$(document).ready( function () {
    var isTemplateReady = !!$('#awp_wizard').length;
    if(!isTemplateReady) {
        return;
    }

    /*
    * Button text changing issue
    *
    * here is not available PS updateCart event
    * we updateProduct event to set the trigger
    * then after all node replacements happened in core.js we catch updatedProduct event to do our logic
    */
    var no_customize = true;

    prestashop.on("updateProduct", function (e) {
        // add to cart event coming without eventType unlike others
        if(undefined === e.eventType) {
            no_customize = false;
        }
    });

    prestashop.on("updatedProduct", function (e) {
        showHideDefaultElementsAWP(no_customize);
        awp_price_update();
        no_customize = true;
    });

    // end button text changing issue

    prestashop.on('clickQuickView', function (elm) {
        var data = {
            'action': 'quickview',
            'id_product': elm.dataset.idProduct,
            'id_product_attribute': elm.dataset.idProductAttribute
        };
        $.post(prestashop.urls.pages.product, data, null, 'json').done(function (resp) {

            var productModal = $('#quickview-modal-' + resp.product.id + '-' + resp.product.id_product_attribute);
            productModal.modal('show');
            productModal.on('hidden.bs.modal', function () {
                productModal.remove();
            });
            showHideDefaultElementsAWP();
        }).fail(function (resp) {
            prestashop.emit('handleError', {eventType: 'clickQuickView', resp: resp});
        });

    });


    if (awp_disable_url_hash)
        initLocationChange();


});
function getProductAttribute()
{
    product_attribute_id = $('#idCombination').val();
    product_id = $('#product_page_product_id').val();

    /* get every attributes values */
    request = '';
    /* create a temporary 'tab_attributes' array containing the choices of the customer */
    var tab_attributes = [];
    for (var i in awp_selected_groups_multiple)
        for (var a in awp_selected_groups_multiple[i])
            tab_attributes.push(awp_selected_groups_multiple[i][a]);

    for (var i in attributesCombinations)
        for (var a in tab_attributes)
            if (attributesCombinations[i]['id_attribute'] === tab_attributes[a])
                request += '/' + attributesCombinations[i]['id_attribute']+ attribute_anchor_separator+attributesCombinations[i]['group'] + attribute_anchor_separator + attributesCombinations[i]['attribute'];
    request = request.replace(request.substring(0, 1), '#/');
    url = window.location + '';

    /* redirection */
    if (url.indexOf('#') != -1)
        url = url.substring(0, url.indexOf('#'));



    /* set ipa to the customization form */
    $('#customizationForm').attr('action', $('#customizationForm').attr('action') + request);
    if (!awp_isQuickView) { 
        window.location = url + request;
    }

}
function initLocationChange(time)
{
    if(!time) time = 500;
    setInterval(checkUrl, time);
}

first_url_check = true;
current_url = prestashop.urls.current_url;

function checkUrl()
{
    if (awp_isQuickView) {
        return;
    }

    /* Remove checked attribute to avoid forced default checkbox attribute selection.
     This function will later add correct checked attributes by parsing the URL hash. */
    if(first_url_check)
    {
        $('.awp_box .awp_attribute_selected').each((i, val) => {
            if($(val).prop('type') == 'checkbox')
                $(val).removeAttr('checked');
        });
    }

    if (current_url != window.location + '' || first_url_check)
    {
        current_url = window.location + '';
        if(awp_psv < 1.6 || typeof attribute_anchor_separator == 'undefined')
            attribute_anchor_separator = '-';
        first_url_check = false;
        url = window.location + '';

        /* if we need to load a specific combination */
        if (url.indexOf('#/') != -1)
        {
            /* get the params to fill from a "normal" url */
            params = url.substring(url.indexOf('#') + 1, url.length);
            tabParams = params.split('/');
            tabValues = [];
            if (tabParams[0] == '')
                tabParams.shift();
            for (var i in tabParams)
                tabValues.push(tabParams[i].split(attribute_anchor_separator));
            product_id = $('#product_page_product_id').val();
            /* fill html with values */
            $('.color_pick').removeClass('selected');
            $('.color_pick').parent().parent().children().removeClass('selected');
            count = 0;
            for (var z in tabValues)
                for (var a in attributesCombinations)
                {
                   if ((attributesCombinations[a]['id_attribute'] == decodeURIComponent(tabValues[z][0])) || (
							(attributesCombinations[a]['group'] === decodeURIComponent(tabValues[z][0])
								&& attributesCombinations[a]['attribute'] === tabValues[z][1])))
                   {
                        count++;
                        /*  add class 'selected' to the selected color */
                        $('#color_' + attributesCombinations[a]['id_attribute']).addClass('selected');
                        $('#color_' + attributesCombinations[a]['id_attribute']).parent().addClass('selected');


                        $('input:radio[name=awp_group_' + attributesCombinations[a]['id_attribute_group'] + ']').removeAttr('checked');
                        //$('input:radio[name=awp_group_' + attributesCombinations[a]['id_attribute_group'] + ']').prop('checked', false);
                        $('input:radio[value=' + attributesCombinations[a]['id_attribute'] + ']').attr('checked', true);
                        $('input:radio[value=' + attributesCombinations[a]['id_attribute'] + ']').prop('checked', true);

                        $('input[type=hidden][name=group_' + attributesCombinations[a]['id_attribute_group'] + ']').val(attributesCombinations[a]['id_attribute']);
                        $('select[name=group_' + attributesCombinations[a]['id_attribute_group'] + ']').val(attributesCombinations[a]['id_attribute']);
                        /* Set the selected options */
                        /*
                         *
                         $("#awp_checkbox_group_"+attributesCombinations[a]['id_attribute']).attr('checked', true);
                        */
                        $("#awp_checkbox_group_"+attributesCombinations[a]['id_attribute']).prop('checked', true);
                        $('#awp_radio_group_'+attributesCombinations[a]['id_attribute']).attr('checked', true);
                        $('select[name=awp_group_' + attributesCombinations[a]['id_attribute_group'] + ']').val(attributesCombinations[a]['id_attribute']);
                        awp_toggle_img(attributesCombinations[a]['id_attribute_group'], attributesCombinations[a]['id_attribute']);
                    }
                }
            /* find combination */

            awp_price_update();

            if (count >= 0)
            {
                /* DISABLED findCombination(false); */
                original_url = url;

                return true;
            }
            /* no combination found = removing attributes from url */
            else
            {
                console.log('changing URL');
                window.location = url.substring(0, url.indexOf('#'));
            }


        }
    }
    return false;
}


$(document).ready(function(){
    $('.awpquickview').on('click', function(e){
        e.preventDefault();
        $this = $(this);
        $idProduct = $this.attr('data-product-id');
        $idProductAttribute = $this.attr('data-product-attribute-id');
        data = {
          'action': 'quickview',
          'id_product': $idProduct,
          'id_product_attribute': $idProductAttribute
        };
        
        $.post(prestashop.urls.pages.product, data, null, 'json').then(function (resp) {
            $imgs = $(resp.quickview_html).find('.images-container').html();
            $('body').append(resp.quickview_html);
            productModal = $('#quickview-modal-' + $idProduct + '-' + $idProductAttribute);
            
            //productConfig(productModal);
            productModal.on('hidden.bs.modal', function () {
                productModal.remove();
            });
            function timeoutImgs($idProduct, $idProductAttribute, $imgs) {
                productModal = $('#quickview-modal-' + $idProduct + '-' + $idProductAttribute);
                productModal.find('.images-container').html($imgs);  
                
                productModal.modal('show');
            }
            setTimeout(timeoutImgs, 500, $idProduct, $idProductAttribute, $imgs); 
            
            
        }).fail((resp) => {
          
        });
    });

    $('.awp_cell_cont').on('click', function(){
        $this = $(this);
        var parent_group_names = $(this).parents('.awp_box_inner').attr('data-parent-group-name');
        parent_group_names = parent_group_names.split(',');
        parent_group_names = parent_group_names.map(function (el) {
          return el.trim();
        });

        var parent_group_names = parent_group_names.filter(function (el) {
          return el != '';
        });

        if(parent_group_names) {
            parent_group_names.forEach(function(value){
                $('[data-parent-group-name*="'+value+'"] input[type="radio"]').removeAttr('checked');
            });

            $(this).find('input[type="radio"]').attr('checked', 'checked');
            $(this).find('input[type="radio"]').prop('checked', true);
        }
    });

     $('.awp_box_inner').each(function(){
        var parent_group_names = $(this).attr('data-parent-group-name');
        parent_group_names = parent_group_names.split(',');
        parent_group_names = parent_group_names.map(function (el) {
          return el.trim();
        });

        var parent_group_names = parent_group_names.filter(function (el) {
          return el != '';
        });

        if(parent_group_names) {
            parent_group_names.forEach(function(value){
                if($('[data-parent-group-name*="'+value+'"]').length > 1){
                    $('[data-parent-group-name*="'+value+'"] input[type="radio"]').removeAttr('checked');
                }
            });
        }
     });
    
});
