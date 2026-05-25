(function (window, $) {
    'use strict';

    function readModuleConfig() {
        var bootstrap = window.BdtAdminApiBiggoptiBootstrap || {};
        var configKey = bootstrap.configVar || '';
        var cfg = configKey && window[configKey] ? window[configKey] : {};

        if ( !cfg.pluginId && cfg.legacyConfigKey && window[cfg.legacyConfigKey] ) {
            cfg = window[cfg.legacyConfigKey];
        }

        return cfg;
    }

    var CFG = readModuleConfig();

    var REGISTRY_KEY = CFG.registryKey || 'BdtAdminApiBiggopti';
    var registry = window[REGISTRY_KEY];
    var BIGGOPTI_DISPLAY_ID_PREFIX = CFG.displayIdPrefix || 'bdt-admin-biggopti-api-biggopti-';
    var LEGACY_DISPLAY_ID_PREFIXES = CFG.legacyDisplayIdPrefixes || [];
    var BIGGOPTI_NOTICE_CLASS = CFG.noticeClass || '';
    var ADMIN_API_BIGGOPTI_SELECTOR = '[data-bdt-admin-api-biggopti="1"]';

    function normalizeDisplayId(displayId) {
        var normalized = displayId == null ? '' : String(displayId).trim();
        var prefixes = [BIGGOPTI_DISPLAY_ID_PREFIX].concat(LEGACY_DISPLAY_ID_PREFIXES || []);
        var i;

        if (!normalized) {
            return '';
        }

        for (i = 0; i < prefixes.length; i++) {
            if (prefixes[i] && normalized.indexOf(prefixes[i]) === 0) {
                normalized = normalized.substring(prefixes[i].length);
                break;
            }
        }

        return normalized;
    }

    function seedDismissals(cfg) {
        var dismissed = {};
        var ids = (cfg && cfg.dismissedDisplayIds) || [];
        for (var i = 0; i < ids.length; i++) {
            var normalizedId = normalizeDisplayId(ids[i]);
            if (normalizedId) {
                dismissed[normalizedId] = true;
            }
        }
        return dismissed;
    }

    if (registry && registry.initialized) {
        if (CFG.pluginId) {
            registry.peers = registry.peers || {};
            registry.peers[CFG.pluginId] = true;
        }
        if (CFG.dismissedDisplayIds && CFG.dismissedDisplayIds.length) {
            for (var p = 0; p < CFG.dismissedDisplayIds.length; p++) {
                var peerDismissedId = normalizeDisplayId(CFG.dismissedDisplayIds[p]);
                if (peerDismissedId) {
                    registry.dismissed[peerDismissedId] = true;
                }
            }
        }
        enforceSingleAdminApiBiggopti();
        return;
    }

    var BIGGOPTI_API_URL = CFG.apiUrl || '';
    var BIGGOPTI_PRODUCT_SLUG = CFG.productSlug || '';
    var BIGGOPTI_LIST_SLUG = CFG.listSlug || BIGGOPTI_PRODUCT_SLUG;
    var ACTIVE_LIST_SLUGS = CFG.activeListSlugs || [];
    var BIGGOPTI_PLUGIN_OPTIONS_PAGE = CFG.pluginOptionsPage || '';
    var BIGGOPTI_SUBMENU_SELECTOR = CFG.submenuSelector || '';
    var BIGGOPTI_DISMISS_ACTION = CFG.dismissAction || 'bdt_admin_api_biggopti_dismiss';
    var FALLBACK = CFG.fallbackPromo || {};
    var PROMO_SUPPRESSION = CFG.promoSuppression || {};

    registry = window[REGISTRY_KEY] = {
        version: 1,
        initialized: true,
        controllerId: CFG.pluginId || 'unknown',
        peers: {},
        dismissed: seedDismissals(CFG),
        activeBiggoptiDisplayId: null,
        biggoptiShown: false,
        promoMenuInjected: false,
        fetchPromise: null,
        apiData: null,

        isDismissed: function (displayId) {
            var normalizedId = normalizeDisplayId(displayId);

            if (!normalizedId) {
                return false;
            }

            return !!this.dismissed[normalizedId];
        },

        markDismissed: function (displayId) {
            var normalizedId = normalizeDisplayId(displayId);

            if (!normalizedId) {
                return;
            }

            this.dismissed[normalizedId] = true;
            this.biggoptiShown = true;
            this.activeBiggoptiDisplayId = null;
            try {
                window.dispatchEvent(new CustomEvent('bdt:admin-api-biggopti:dismissed', {
                    detail: { displayId: normalizedId }
                }));
            } catch (e) {
                // Ignore custom event failures.
            }
        },

        hasVisibleBiggopti: function () {
            return !!document.querySelector(ADMIN_API_BIGGOPTI_SELECTOR);
        },

        canRenderBiggopti: function (displayId) {
            if (this.biggoptiShown) {
                return false;
            }
            if (!displayId || this.isDismissed(displayId)) {
                return false;
            }
            if (this.activeBiggoptiDisplayId && this.activeBiggoptiDisplayId !== displayId) {
                return false;
            }
            if (this.hasVisibleBiggopti()) {
                return false;
            }
            if (document.querySelector('[id^="' + BIGGOPTI_DISPLAY_ID_PREFIX + '"]')) {
                return false;
            }
            return true;
        }
    };

    /**
     * Strict guard: never allow more than one admin API biggopti notice in the DOM.
     */
    function enforceSingleAdminApiBiggopti() {
        var nodes = document.querySelectorAll(ADMIN_API_BIGGOPTI_SELECTOR);
        var i;

        if (nodes.length > 1) {
            for (i = 1; i < nodes.length; i++) {
                nodes[i].parentNode && nodes[i].parentNode.removeChild(nodes[i]);
            }
        }

        // Legacy / duplicate API nodes without the data flag.
        var legacyNodes = document.querySelectorAll('[id^="' + BIGGOPTI_DISPLAY_ID_PREFIX + '"]');
        if (legacyNodes.length > 1) {
            for (i = 1; i < legacyNodes.length; i++) {
                var legacyEl = legacyNodes[i].closest('.' + BIGGOPTI_NOTICE_CLASS) || legacyNodes[i];
                legacyEl.parentNode && legacyEl.parentNode.removeChild(legacyEl);
            }
        }

        var remaining = document.querySelector(ADMIN_API_BIGGOPTI_SELECTOR) || document.querySelector('[id^="' + BIGGOPTI_DISPLAY_ID_PREFIX + '"]');
        if (remaining) {
            registry.biggoptiShown = true;
            registry.activeBiggoptiDisplayId = remaining.getAttribute('data-display-id')
                || (remaining.id ? remaining.id.replace(BIGGOPTI_DISPLAY_ID_PREFIX, '') : null);
        }
    }

    function initAPIBiggoptiCountdown() {
        jQuery('.bdt-biggopti-countdown').each(function () {
            var $countdown = jQuery(this);
            if ($countdown.data('bdt-countdown-init')) {
                return;
            }
            $countdown.data('bdt-countdown-init', true);

            var $timer = $countdown.find('.countdown-timer');
            var endDate = $countdown.data('end-date');
            var timezone = $countdown.data('timezone');

            if (!endDate || !$timer.length) {
                return;
            }

            function updateCountdown() {
                var endTime = new Date(endDate + ' ' + timezone).getTime();
                var distance = endTime - Date.now();

                if (distance < 0) {
                    $countdown.hide();
                    return;
                }

                var days = Math.floor(distance / (1000 * 60 * 60 * 24));
                var hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                var minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                var seconds = Math.floor((distance % (1000 * 60)) / 1000);

                days = days < 10 ? '0' + days : days;
                hours = hours < 10 ? '0' + hours : hours;
                minutes = minutes < 10 ? '0' + minutes : minutes;
                seconds = seconds < 10 ? '0' + seconds : seconds;

                var countdownText = '';
                if (days > 0) {
                    countdownText += '<div class="countdown-item"><span class="number">' + days + '</span><span class="label">days</span></div><span class="separator"></span>';
                }
                countdownText += '<div class="countdown-item"><span class="number">' + hours + '</span><span class="label">hrs</span></div><span class="separator"></span>';
                countdownText += '<div class="countdown-item"><span class="number">' + minutes + '</span><span class="label">min</span></div><span class="separator"></span>';
                countdownText += '<div class="countdown-item"><span class="number">' + seconds + '</span><span class="label">sec</span></div>';

                $timer.html(countdownText);
            }

            updateCountdown();
            setInterval(updateCountdown, 1000);
        });
    }

    $(document).on('click', '.' + BIGGOPTI_NOTICE_CLASS + '.is-dismissible .bdt-admin-api-biggopti-dismiss', function (e) {
        e.preventDefault();

        var $this = $(this).closest('.' + BIGGOPTI_NOTICE_CLASS);
        var displayId = normalizeDisplayId(
            $this.data('display-id') || $this.attr('data-display-id') || ''
        );

        if (!displayId && $this.attr('id')) {
            displayId = normalizeDisplayId($this.attr('id'));
        }

        if (!displayId) {
            return;
        }

        registry.markDismissed(displayId);

        var ajaxUrl = CFG.ajaxurl || (typeof ajaxurl !== 'undefined' ? ajaxurl : '');

        $(ADMIN_API_BIGGOPTI_SELECTOR).add('[id^="' + BIGGOPTI_DISPLAY_ID_PREFIX + '"]').fadeTo(50, 0, function () {
            $(this).slideUp(50, function () {
                $(this).remove();
            });
        });

        enforceSingleAdminApiBiggopti();

        if (!ajaxUrl || !CFG.nonce) {
            return;
        }

        $.ajax({
            url: ajaxUrl,
            type: 'POST',
            data: {
                action: BIGGOPTI_DISMISS_ACTION,
                display_id: displayId,
                id: $this.attr('id'),
                _wpnonce: CFG.nonce
            }
        });
    });

    function normalizeAllBiggoptiRecords(raw) {
        if (!raw) {
            return [];
        }

        if (Array.isArray(raw)) {
            return raw.filter(function (item) {
                return item && item.type === 'adminDashboard';
            });
        }

        if (typeof raw === 'object') {
            var offers = [];
            Object.keys(raw).forEach(function (key) {
                if (Array.isArray(raw[key])) {
                    offers = offers.concat(raw[key]);
                }
            });
            return offers.filter(function (item) {
                return item && item.type === 'adminDashboard';
            });
        }

        return [];
    }

    function isPromoSuppressed(item) {
        if (!item) {
            return false;
        }

        return !!PROMO_SUPPRESSION.hideAuthorOffers;
    }

    function normalizePluginListSlug(slug) {
        return slug == null ? '' : String(slug).trim().toLowerCase();
    }

    function normalizePluginList(list) {
        if (!Array.isArray(list)) {
            return [];
        }

        var normalized = [];
        var i;

        for (i = 0; i < list.length; i++) {
            var slug = normalizePluginListSlug(list[i]);

            if (slug) {
                normalized.push(slug);
            }
        }

        return normalized;
    }

    function isBlockedByBlackList(blackList, currentSlug) {
        var i;
        var j;

        for (i = 0; i < blackList.length; i++) {
            if (blackList[i] === currentSlug) {
                return true;
            }
        }

        for (i = 0; i < blackList.length; i++) {
            for (j = 0; j < ACTIVE_LIST_SLUGS.length; j++) {
                if (blackList[i] === normalizePluginListSlug(ACTIVE_LIST_SLUGS[j])) {
                    return true;
                }
            }
        }

        return false;
    }

    function isAllowedByPluginList(item) {
        if (!item) {
            return false;
        }

        var currentSlug = normalizePluginListSlug(BIGGOPTI_LIST_SLUG);

        if (!currentSlug) {
            return true;
        }

        var blackList = normalizePluginList(item.black_list);
        var whiteList = normalizePluginList(item.white_list);

        // black_list first: host slug or any active mapped plugin on the site.
        if (isBlockedByBlackList(blackList, currentSlug)) {
            return false;
        }

        if (whiteList.length && whiteList.indexOf(currentSlug) === -1) {
            return false;
        }

        return true;
    }

    function isPromoItemValid(item) {
        if (!item || item.type !== 'adminDashboard') {
            return false;
        }

        if (isPromoSuppressed(item)) {
            return false;
        }

        if (!isAllowedByPluginList(item)) {
            return false;
        }

        if (!item.is_enabled || !item.end_date) {
            return false;
        }

        var tz = item.timezone || 'UTC';
        var endStr = (item.end_date + '').replace(' ', 'T') + (tz === 'UTC' ? 'Z' : '');
        var endDate = new Date(endStr);

        return !isNaN(endDate.getTime()) && Date.now() <= endDate.getTime();
    }

    var BIGGOPTI_HTML_DISCARD = {
        script: true, style: true, iframe: true, object: true, embed: true,
        svg: true, math: true, form: true, input: true, textarea: true,
        select: true, button: true, meta: true, link: true, base: true
    };
    var BIGGOPTI_HTML_ALLOWED = {
        br: {},
        span: { style: true },
        strong: {}, em: {}, b: {}, i: {}, u: {}, small: {}, mark: {}, p: {}, div: {},
        a: { href: true, target: true, rel: true }
    };

    function escPlain(s) {
        return (s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function sanitizeBiggoptiInlineStyle(style) {
        if (!style || typeof style !== 'string') {
            return '';
        }

        var parts = style.split(';');
        var out = [];

        for (var i = 0; i < parts.length; i++) {
            var chunk = parts[i].trim();
            if (!chunk) continue;
            var colon = chunk.indexOf(':');
            if (colon === -1) continue;
            var prop = chunk.slice(0, colon).trim().toLowerCase();
            var val = chunk.slice(colon + 1).trim();
            if (!val || /expression\s*\(|url\s*\(\s*['"]?\s*javascript/i.test(val)) continue;
            if (prop === 'color' && (/^#[0-9a-f]{3,8}$/i.test(val) || /^rgba?\([^)]*\)$/i.test(val))) {
                out.push('color: ' + val);
            } else if (prop === 'font-weight' && /^(bold|normal|bolder|lighter|[1-9]00)$/i.test(val)) {
                out.push('font-weight: ' + val);
            }
        }

        return out.join('; ');
    }

    function stripBiggoptiUnsafeAttrs(el, tag) {
        var allowed = BIGGOPTI_HTML_ALLOWED[tag];
        var attrs = el.attributes ? [].slice.call(el.attributes) : [];

        for (var j = 0; j < attrs.length; j++) {
            var attr = attrs[j];
            var name = attr.name.toLowerCase();

            if (name.indexOf('on') === 0) {
                el.removeAttribute(attr.name);
                continue;
            }

            if (tag === 'a') {
                if (name === 'href') {
                    var href = ('' + attr.value).replace(/[\u0000-\u001f\u007f]/g, '').trim();
                    if (/^javascript:/i.test(href) || /^data:/i.test(href) || /^vbscript:/i.test(href)) {
                        el.removeAttribute('href');
                    } else if (/^https?:\/\//i.test(href) || /^mailto:/i.test(href)) {
                        el.setAttribute('href', href);
                    } else {
                        el.removeAttribute('href');
                    }
                } else if (name === 'target' && /^_blank$/i.test(attr.value)) {
                    continue;
                } else if (name === 'rel') {
                    continue;
                } else {
                    el.removeAttribute(attr.name);
                }
                continue;
            }

            if (tag === 'span' && name === 'style') {
                var cleaned = sanitizeBiggoptiInlineStyle(attr.value);
                el.removeAttribute('style');
                if (cleaned) {
                    el.setAttribute('style', cleaned);
                }
                continue;
            }

            if (!allowed[name]) {
                el.removeAttribute(attr.name);
            }
        }

        if (tag === 'a' && el.getAttribute('target') && /^_blank$/i.test(el.getAttribute('target'))) {
            var rel = el.getAttribute('rel') || '';
            if (!/noopener/i.test(rel)) {
                el.setAttribute('rel', ((rel ? rel + ' ' : '') + 'noopener noreferrer').trim());
            }
        }
    }

    function sanitizeBiggoptiDom(root) {
        var node = root.firstChild;

        while (node) {
            var next = node.nextSibling;

            if (node.nodeType === 1) {
                var tag = node.tagName.toLowerCase();

                if (BIGGOPTI_HTML_DISCARD[tag]) {
                    root.removeChild(node);
                } else if (!BIGGOPTI_HTML_ALLOWED[tag]) {
                    while (node.firstChild) {
                        root.insertBefore(node.firstChild, node);
                    }
                    root.removeChild(node);
                } else {
                    stripBiggoptiUnsafeAttrs(node, tag);
                    sanitizeBiggoptiDom(node);
                }
            }

            node = next;
        }
    }

    function sanitizeBiggoptiRichHtml(raw) {
        if (!raw || typeof raw !== 'string') {
            return '';
        }

        var wrapped = '<div class="bdt-biggopti-sanitize-root">' + raw + '</div>';
        var doc;

        try {
            doc = new DOMParser().parseFromString(wrapped, 'text/html');
        } catch (e) {
            return escPlain(raw);
        }

        var root = doc.body.querySelector('.bdt-biggopti-sanitize-root');
        if (!root) {
            return escPlain(raw);
        }

        sanitizeBiggoptiDom(root);
        return root.innerHTML;
    }

    function getCurrentVisibilitySectors() {
        var phpSector = CFG.currentSector || '';
        if (phpSector) {
            return [phpSector];
        }

        var path = (window.location.pathname || '').toLowerCase();
        var search = (window.location.search || '');
        var pageRaw = (search.match(/[?&]page=([^&]+)/i) || [])[1] || '';
        var page = '';

        try {
            page = pageRaw ? (decodeURIComponent(pageRaw.replace(/\+/g, ' ')) || pageRaw) : '';
        } catch (e) {
            page = pageRaw || '';
        }

        var sectors = [];
        var pathBase = path.split('?')[0];

        if (path.indexOf('wp-admin') !== -1 && (pathBase.indexOf('index.php') !== -1 || /\/wp-admin\/?$/.test(pathBase))) {
            sectors.push('wp_dashboard');
        }
        if (page && (page === BIGGOPTI_PLUGIN_OPTIONS_PAGE || page.toLowerCase().indexOf(BIGGOPTI_PRODUCT_SLUG.replace('-', '_')) !== -1)) {
            sectors.push('plugin_dashboard');
        }
        if (path.indexOf('themes.php') !== -1) {
            sectors.push('themes_page');
        }
        if (path.indexOf('options-') !== -1) {
            sectors.push('settings_page');
        }
        if (path.indexOf('profile.php') !== -1 || path.indexOf('user-edit.php') !== -1 || path.indexOf('user-new.php') !== -1) {
            sectors.push('user_page');
        }
        if (path.indexOf('plugins.php') !== -1 || path.indexOf('plugin-install.php') !== -1 || (path.indexOf('admin.php') !== -1 && page)) {
            sectors.push('plugin_pages');
        }
        if (path.indexOf('tools.php') !== -1) {
            sectors.push('tools_page');
        }

        return sectors;
    }

    function isItemVisibleForCurrentSector(item) {
        var sectors = item.visibility_sectors;
        if (!sectors || !Array.isArray(sectors) || sectors.length === 0) {
            return true;
        }

        var current = getCurrentVisibilitySectors();
        for (var i = 0; i < current.length; i++) {
            if (sectors.indexOf(current[i]) !== -1) {
                return true;
            }
        }

        return false;
    }

    function renderBiggoptiHTML(item) {
        var esc = function (s) { return escPlain(s); };
        var displayId = normalizeDisplayId(item.display_id || item.id || 'default');

        if (!registry.canRenderBiggopti(displayId) || !isItemVisibleForCurrentSector(item)) {
            return '';
        }

        var bg = (item.background_color || '') + (item.image ? ' background-image:url(' + esc(item.image) + ')' : '');
        var wrapperClass = 'bdt-biggopti-wrapper' + (item.image ? ' has-background-image' : '');
        var title = esc(item.title || '');
        var content = sanitizeBiggoptiRichHtml(item.content || '');
        var logoUrl = item.logo || '';
        var link = item.link || '';
        var btnText = item.button_text || 'Read More';
        var showCountdown = item.show_countdown && item.end_date;
        var endDate = item.end_date || '';
        var tz = item.timezone || 'UTC';
        var biggoptiId = BIGGOPTI_DISPLAY_ID_PREFIX + displayId;
        var countdownContent = item.countdown_content || '';

        var countdownHtml = showCountdown
            ? '<div class="bdt-biggopti-countdown" data-end-date="' + esc(endDate) + '" data-timezone="' + esc(tz) + '"><div class="countdown-timer">Loading...</div></div>'
            : '<div class="bdt-biggopti-countdown"><div class="countdown-content">' + esc(countdownContent) + '</div></div>';

        var btnHtml = link
            ? '<div class="bdt-biggopti-btn"><a href="' + esc(link) + '" target="_blank"><div class="nm-biggopti-btn">' + esc(btnText) + ' <span class="dashicons dashicons-arrow-right-alt"></span></div></a></div>'
            : '';

        var logoHtml = logoUrl
            ? '<div class="bdt-biggopti-logo-wrapper"><img width="100" src="' + esc(logoUrl) + '" alt="Logo"></div>'
            : '';

        var inner = '<div class="' + wrapperClass + '"' + (bg ? ' style="' + esc(bg) + '"' : '') + '>' +
            '<div class="bdt-api-biggopti-content">' +
            '<div class="bdt-biggopti-content">' +
            '<div class="bdt-biggopti-content-inner">' + logoHtml +
            '<div class="bdt-biggopti-title-description">' +
            (title ? '<h2 class="bdt-biggopti-title">' + title + '</h2>' : '') +
            (content ? '<div class="bdt-biggopti-html-content">' + content + '</div>' : '') +
            '</div></div>' +
            '<div class="bdt-biggopti-content-right">' + countdownHtml + btnHtml + '</div>' +
            '</div></div></div>';

        var attrs = 'id="' + biggoptiId + '" data-bdt-admin-api-biggopti="1"';
        attrs += ' data-display-id="' + esc(displayId) + '"';

        var dismissBtn = '<button type="button" class="bdt-admin-api-biggopti-dismiss dashicons dashicons-dismiss"><span class="screen-reader-text">Dismiss this biggopti.</span></button>';

        return '<div class="' + escPlain(BIGGOPTI_NOTICE_CLASS) + ' biggopti biggopti-info is-dismissible" ' + attrs + '>' + inner + dismissBtn + '</div>';
    }

    function renderFeedHTML(item) {
        var imageUrl = item.feed_image || '';
        if (!imageUrl) {
            return '';
        }

        var displayId = item.display_id || item.id || 'default';
        var feedId = 'bdt-admin-api-feed-' + displayId;

        return '<div id="' + escPlain(feedId) + '" class="bdt-dashboard-feed">' +
            '<a href="' + escPlain(item.link || '#') + '" target="_blank" rel="noopener noreferrer">' +
            '<img src="' + escPlain(imageUrl) + '" alt="" style="max-width:100%; height:auto;">' +
            '</a></div>';
    }

    function isExcludedUrl() {
        var url = window.location.href || '';
        var patterns = ['plugin-install.php', 'theme-install.php', 'action=upload-plugin', 'action=upload-theme'];

        for (var i = 0; i < patterns.length; i++) {
            if (url.indexOf(patterns[i]) !== -1) {
                return true;
            }
        }

        return false;
    }

    function pickAdminApiBiggopti(list) {
        var seen = {};

        for (var i = 0; i < list.length; i++) {
            var item = list[i];
            if (!isPromoItemValid(item)) {
                continue;
            }

            var displayId = normalizeDisplayId(item.display_id || item.id || 'default-' + i);
            if (seen[displayId] || registry.isDismissed(displayId)) {
                continue;
            }
            seen[displayId] = true;

            if (registry.canRenderBiggopti(displayId)) {
                return item;
            }
        }

        return null;
    }

    function injectAdminApiBiggopti(data) {
        enforceSingleAdminApiBiggopti();

        if (registry.biggoptiShown || isExcludedUrl() || registry.hasVisibleBiggopti()) {
            enforceSingleAdminApiBiggopti();
            return;
        }

        var biggopti = pickAdminApiBiggopti(normalizeAllBiggoptiRecords(data));
        if (!biggopti) {
            return;
        }

        var displayId = normalizeDisplayId(biggopti.display_id || biggopti.id || 'default');

        if (registry.isDismissed(displayId) || !registry.canRenderBiggopti(displayId)) {
            return;
        }

        var html = renderBiggoptiHTML(biggopti);

        if (!html) {
            return;
        }

        var $target = $('#wpbody-content .wrap').first();
        if (!$target.length) {
            $target = $('.wrap').first();
        }
        if (!$target.length) {
            $target = $('#wpbody-content');
        }

        var $markup = $(html);

        if ($target.children('hr.wp-header-end').length) {
            $target.children('hr.wp-header-end').first().after($markup);
        } else if ($target.children('h1').length) {
            $target.children('h1').first().after($markup);
        } else {
            $target.prepend($markup);
        }

        registry.activeBiggoptiDisplayId = displayId;
        registry.biggoptiShown = true;
        enforceSingleAdminApiBiggopti();
        initAPIBiggoptiCountdown();
    }

    function injectFeedsFromData(data) {
        var list = normalizeAllBiggoptiRecords(data);
        if (!list.length) {
            return;
        }

        var $dashboard = $('#bdt-dashboard-overview .inside');
        if (!$dashboard.length) {
            $dashboard = $('#bdt-dashboard-overview');
        }
        if (!$dashboard.length) {
            return;
        }

        var html = '';

        for (var i = 0; i < list.length; i++) {
            var item = list[i];

            if (isPromoSuppressed(item) || !isAllowedByPluginList(item)) {
                continue;
            }

            var did = item.display_id || item.id || 'default-' + i;
            if ($('#bdt-admin-api-feed-' + did).length) {
                continue;
            }

            html += renderFeedHTML(item);
        }

        if (html) {
            $dashboard.prepend($(html));
        }
    }

    function getFirstValidPromo(data) {
        var list = normalizeAllBiggoptiRecords(data);

        for (var i = 0; i < list.length; i++) {
            if (isPromoItemValid(list[i]) && list[i].link) {
                var title = list[i].sub_title;
                if (title == null || title === '') {
                    title = list[i].button_text || list[i].title || null;
                }
                return { sub_title: title, link: list[i].link };
            }
        }

        return null;
    }

    var PROMO_SECTORS = ['wp_dashboard', 'plugin_dashboard', 'themes_page', 'settings_page', 'user_page', 'plugin_pages', 'tools_page'];

    function isCurrentSectorAllowedForPromo() {
        var current = getCurrentVisibilitySectors();
        for (var i = 0; i < current.length; i++) {
            if (PROMO_SECTORS.indexOf(current[i]) !== -1) {
                return true;
            }
        }
        return current.length === 0;
    }

    function injectPromotionMenu(promo) {
        if (registry.promoMenuInjected) {
            return;
        }

        var isPro = !!CFG.isPro;
        if (isPro && !promo) {
            return;
        }

        var adminSubmenu = document.querySelector(BIGGOPTI_SUBMENU_SELECTOR);
        if (!adminSubmenu || adminSubmenu.querySelector('.bdt-promo-menu-item')) {
            return;
        }

        var p = promo || FALLBACK;
        var href = escPlain(p.link || '');
        var text = escPlain(p.sub_title || FALLBACK.sub_title || '');
        var html = '<li class="bdt-promo-menu-item"><a href="' + href + '" target="_blank" style="color: #FE506C; font-weight: 600;" rel="noopener noreferrer">' + text + '</a></li>';

        adminSubmenu.insertAdjacentHTML('beforeend', html);
        registry.promoMenuInjected = true;
    }

    function processApiData(data) {
        if (registry.apiData) {
            enforceSingleAdminApiBiggopti();
            return;
        }

        registry.apiData = data;
        window.bdtPromoData = data;

        injectAdminApiBiggopti(data);
        enforceSingleAdminApiBiggopti();
        injectFeedsFromData(data);

        var promo = getFirstValidPromo(data);

        if (isCurrentSectorAllowedForPromo()) {
            injectPromotionMenu(promo);
        }
    }

    function fetchPromoData() {
        if (registry.fetchPromise) {
            return registry.fetchPromise;
        }

        if (CFG.useTestApiResponse && CFG.testApiData) {
            registry.fetchPromise = Promise.resolve(CFG.testApiData).then(function (data) {
                processApiData(data);
                return data;
            });
            return registry.fetchPromise;
        }

        registry.fetchPromise = fetch(BIGGOPTI_API_URL)
            .then(function (response) {
                return response.json();
            })
            .then(function (data) {
                processApiData(data);
                return data;
            })
            .catch(function () {
                if (isCurrentSectorAllowedForPromo() && !CFG.isPro) {
                    injectPromotionMenu(FALLBACK);
                }
                return null;
            });

        return registry.fetchPromise;
    }

    window.addEventListener('bdt:admin-api-biggopti:dismissed', function (event) {
        var displayId = event && event.detail ? event.detail.displayId : '';
        displayId = normalizeDisplayId(displayId);
        if (displayId) {
            registry.markDismissed(displayId);
        }
    });

    $(window).on('load', function () {
        enforceSingleAdminApiBiggopti();
        setTimeout(fetchPromoData, 400);
    });

    $(document).ready(function () {
        enforceSingleAdminApiBiggopti();
    });

})(window, jQuery);
