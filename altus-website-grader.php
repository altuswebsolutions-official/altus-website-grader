<?php
/**
 * Plugin Name: Altus Website Grader
 * Description: Free website grader for small businesses. Grades any public website on 15 speed, SEO, mobile and trust signals and shows the full report on screen. This open-source version contains NO lead capture, NO email sending, and NO data storage.
 * Version: 1.0.0
 * Author: Altus Web Solutions
 * Text Domain: altus-website-grader
 * Requires at least: 5.8
 * Requires PHP: 7.4
 */

if (!defined('ABSPATH')) exit;

define('AWG_VERSION', '1.0.0');
define('AWG_PATH', plugin_dir_path(__FILE__));
define('AWG_URL', plugin_dir_url(__FILE__));

require_once AWG_PATH . 'includes/grader.php';

/* Settings (stripped version: no lead storage, no email — only the consultation link). */
function awg_get_settings() {
    return array(
        'book_url' => 'https://altuswebsolutions.com/schedule-free-consultation/',
    );
}

/* ------------------------------------------------------------------ */
/* Frontend assets (only loaded when the shortcode is present)        */
/* ------------------------------------------------------------------ */
add_action('wp_enqueue_scripts', 'awg_enqueue');
function awg_enqueue() {
    if (is_admin()) return;
    global $post;
    if (!($post instanceof WP_Post) || !has_shortcode($post->post_content, 'altus_website_grader')) return;

    wp_enqueue_style('awg-grader', AWG_URL . 'assets/grader.css', array(), AWG_VERSION);
    wp_enqueue_script('awg-grader', AWG_URL . 'assets/grader.js', array(), AWG_VERSION, true);
    wp_localize_script('awg-grader', 'AWG', array(
        'ajax'  => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('awg_grader'),
    ));
}

/* ------------------------------------------------------------------ */
/* Shortcode: renders the grader UI                                   */
/* ------------------------------------------------------------------ */
add_shortcode('altus_website_grader', 'awg_shortcode');
function awg_shortcode() {
    ob_start();
    ?>
    <div id="awg-root" class="awg-root">
        <!-- Step 1: URL entry -->
        <div class="awg-card" id="awg-step-url">
            <div class="awg-badge">Free tool</div>
            <h2 class="awg-title">How does your website score?</h2>
            <p class="awg-sub">Enter your website address below and we will grade your homepage on speed, SEO, mobile-friendliness and trust signals — in under a minute.</p>
            <form id="awg-url-form" novalidate>
                <label class="awg-label" for="awg-url">Your website address</label>
                <input type="url" id="awg-url" name="url" class="awg-input" placeholder="https://www.yourbusiness.co.uk" required inputmode="url" autocomplete="url">
                <label class="awg-label" for="awg-business-name">Business name <span class="awg-optional">(optional)</span></label>
                <input type="text" id="awg-business-name" name="business_name" class="awg-input" placeholder="e.g. Springfield Plumbing" autocomplete="organization">
                <button type="submit" class="awg-btn" id="awg-run-btn"><span class="awg-btn-text">Grade my website</span></button>
                <p class="awg-error" id="awg-url-error" role="alert" hidden></p>
            </form>
            <p class="awg-fineprint">We only fetch your public homepage. No login or private data is accessed. This is an automated homepage-based grading — not a full Google PageSpeed audit.</p>
        </div>

        <!-- Step 2: scanning progress -->
        <div class="awg-card" id="awg-step-scan" hidden>
            <div class="awg-spinner" aria-hidden="true"></div>
            <h2 class="awg-title">Grading your website…</h2>
            <p class="awg-sub" id="awg-scan-status">Loading homepage…</p>
            <div class="awg-progress"><div class="awg-progress-bar" id="awg-progress-bar"></div></div>
            <p class="awg-fineprint">Measuring 15 speed, SEO, mobile and trust signals.</p>
        </div>

        <!-- Step 3: free results (score + top 3 issues) -->
        <div class="awg-card" id="awg-step-result" hidden>
            <div class="awg-score-wrap">
                <div class="awg-score-ring" id="awg-score-ring"><span id="awg-score-num">0</span><span class="awg-score-pct">%</span></div>
                <div>
                    <div class="awg-grade" id="awg-grade">–</div>
                    <p class="awg-sub" id="awg-result-summary"></p>
                </div>
            </div>
            <h3 class="awg-h3">Top 3 issues to fix</h3>
            <ul class="awg-issues" id="awg-top-issues"></ul>
            <div class="awg-unlock">
                <h3 class="awg-h3">See the full 15-point report</h3>
                <p class="awg-sub">All 15 checks with plain-English explanations and exactly what to fix — shown right here on screen, free.</p>
                <button type="button" class="awg-btn" id="awg-full-report-btn">Show my full report</button>
                <p class="awg-fineprint">This open-source version sends no email and stores nothing: your result is shown on screen only and is not saved.</p>
            </div>
        </div>

        <!-- Step 4: full report -->
        <div class="awg-card" id="awg-step-report" hidden>
            <div class="awg-report-head">
                <div class="awg-score-ring awg-score-ring-sm" id="awg-score-ring-2"><span id="awg-score-num-2">0</span><span class="awg-score-pct">%</span></div>
                <div>
                    <h2 class="awg-title" style="margin:0">Your full website report</h2>
                    <p class="awg-sub" id="awg-report-meta" style="margin:.25rem 0 0"></p>
                </div>
            </div>
            <p class="awg-sub" id="awg-report-note"></p>
            <div class="awg-table-wrap">
                <table class="awg-table" id="awg-report-table">
                    <thead><tr><th>Check</th><th>Result</th><th>What we found</th></tr></thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="awg-cta">
                <h3 class="awg-h3">Want these fixed?</h3>
                <p class="awg-sub">We build fast, high-ranking websites for UK small businesses. Book a free 15-minute consultation and we will talk through your report.</p>
                <a class="awg-btn awg-btn-link" id="awg-book-link" href="https://altuswebsolutions.com/schedule-free-consultation/" target="_blank" rel="noopener">Book a free 15-min consultation</a>
                <p class="awg-fineprint">By Altus Web Solutions — no obligation.</p>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/* ------------------------------------------------------------------ */
/* AJAX: run the grade (step 1 -> step 3)                             */
/* ------------------------------------------------------------------ */
add_action('wp_ajax_awg_run', 'awg_ajax_run');
add_action('wp_ajax_nopriv_awg_run', 'awg_ajax_run');
function awg_ajax_run() {
    check_ajax_referer('awg_grader', 'nonce');

    // Rate limit: 5 checks per IP per hour.
    $ip  = awg_client_ip();
    $key = 'awg_rl_' . md5($ip);
    $hits = (int) get_transient($key);
    if ($hits >= 5) {
        wp_send_json_error(array('message' => 'Too many checks from this address. Please try again later.'));
    }
    set_transient($key, $hits + 1, HOUR_IN_SECONDS);

    $url           = isset($_POST['url']) ? trim(wp_unslash($_POST['url'])) : '';
    $business_name = isset($_POST['business_name']) ? sanitize_text_field(wp_unslash($_POST['business_name'])) : '';

    $url = awg_normalize_url($url);
    if (!$url) {
        wp_send_json_error(array('message' => 'Please enter a valid website address, e.g. https://www.yourbusiness.co.uk'));
    }
    if (!awg_url_allowed($url)) {
        wp_send_json_error(array('message' => 'That address cannot be checked (private or invalid host). Please use the public website.'));
    }

    @set_time_limit(120);

    $checker = new Altus_WG_Checker();
    $result  = $checker->run($url);

    if (!empty($result['fatal'])) {
        wp_send_json_error(array('message' => $result['fatal']));
    }

    // Stripped version: the full result is returned in this response only.
    // Nothing is stored server-side (no lead table, no result transient, no email).

    // Top 3 issues = worst first (fail before improve), non-good only.
    $issues = array();
    $sorted = $result['items'];
    usort($sorted, function ($a, $b) {
        $w = array('fail' => 0, 'improve' => 1, 'good' => 2);
        return $w[$a['status']] - $w[$b['status']];
    });
    foreach ($sorted as $item) {
        if ($item['status'] !== 'good') $issues[] = $item;
        if (count($issues) === 3) break;
    }

    wp_send_json_success(array(
        'score'    => $result['score'],
        'grade'    => $result['grade'],
        'summary'  => $result['summary'],
        'note'     => $result['note'],
        'url'      => $url,
        'book_url' => 'https://altuswebsolutions.com/schedule-free-consultation/',
        'items'    => $result['items'],
        'issues'   => array_map(function ($i) {
            return array('label' => $i['label'], 'status' => $i['status'],
                         'why' => $i['why'], 'detail' => $i['detail']);
        }, $issues),
    ));
}

/* ------------------------------------------------------------------ */
function awg_client_ip() {
    foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'REMOTE_ADDR') as $k) {
        if (!empty($_SERVER[$k])) {
            $ip = trim(explode(',', $_SERVER[$k])[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP)) return $ip;
        }
    }
    return '0.0.0.0';
}

function awg_normalize_url($url) {
    $url = trim($url);
    if ($url === '') return false;
    if (!preg_match('#^https?://#i', $url)) $url = 'https://' . $url;
    $parts = wp_parse_url($url);
    if (empty($parts['host'])) return false;
    // Rebuild cleanly to avoid userinfo / weird ports.
    $host = strtolower($parts['host']);
    $path = isset($parts['path']) ? $parts['path'] : '/';
    return 'https://' . $host . $path;
}

/** SSRF guard: block private/reserved IP ranges after DNS resolution. */
function awg_url_allowed($url) {
    $host = wp_parse_url($url, PHP_URL_HOST);
    if (!$host) return false;
    // Fast path: literal IP.
    if (filter_var($host, FILTER_VALIDATE_IP)) {
        return awg_ip_public($host);
    }
    $ip = gethostbyname($host);
    if ($ip === $host) return false; // DNS failed
    return awg_ip_public($ip);
}

function awg_ip_public($ip) {
    if (!filter_var($ip, FILTER_VALIDATE_IP)) return false;
    // Reject private, reserved, loopback, link-local, multicast.
    return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false;
}

