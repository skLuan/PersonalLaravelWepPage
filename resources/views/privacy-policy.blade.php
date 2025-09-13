@php
    $title = 'Privacy Policy';
@endphp
@extends('layouts.app', ['title' => $title])

@section('content')
    <section aria-labelledby="privacy-heading" class="m-2 mx-4 lg:w-8/12 lg:mx-auto">
        <h1 id="privacy-heading">Privacy Policy</h1>
        <p><time datetime="2025-09-04">Last updated: September 4, 2025</time></p>

        <article>
            <h2 id="intro">Introduction</h2>
            <p>This Privacy Policy explains how Luan Erazo ("I") collects, uses, and
                shares information when you visit our website <a class="p-0" href="{{ url('/') }}">{{ url('/') }}</a>. As a web designer, I use analytics tools
                to improve user interactions and experiment with these tools in a safe environment. I am committed to
                protecting your privacy and being transparent about my practices.</p>
        </article>

        <article>
            <h2 id="info-collected">Information I Collect</h2>
            <p>I collect information automatically through third-party analytics services when you visit my site. This
                includes:</p>
            <ul>
                <li>Behavioral metrics, such as page views, clicks, scrolls, and session duration.</li>
                <li>Device and browser information, such as IP address, browser type, operating system, and approximate
                    location.</li>
                <li>Interaction data, including heatmaps and session replays (recordings of user actions without capturing
                    sensitive inputs like passwords).</li>
            </ul>
            <p>We do not collect personal information through forms or accounts on this site unless explicitly stated
                otherwise.</p>
        </article>

        <article>
            <h2 id="info-use">How We Use Your Information</h2>
            <p>We use the collected data to:</p>
            <ul>
                <li>Analyze and improve user interactions on our website.</li>
                <li>Optimize site performance and design.</li>
                <li>Experiment with analytics tools in a controlled, safe environment.</li>
            </ul>
            <p>We do not use this data for advertising, marketing products/services, or any commercial purposes beyond site
                improvement.</p>
        </article>

        <article>
            <h2 id="cookies">Cookies and Tracking Technologies</h2>
            <p>Our site uses first- and third-party cookies and similar tracking technologies (e.g., pixels, scripts) to
                collect usage data. These help us understand site popularity and user behavior. You can manage cookies
                through your browser settings, but disabling them may affect site functionality.</p>
        </article>

        <article>
            <h2 id="third-party">Third-Party Services</h2>
            <p>We partner with third-party providers to analyze website usage. These services may collect and process data
                on our behalf, and their practices are governed by their own privacy policies.</p>

            <h3 id="clarity">Microsoft Clarity</h3>
            <p>We partner with Microsoft Clarity to capture how you use and interact with our website through behavioral
                metrics, heatmaps, and session replays to improve user interactions and experiment with the tool. Data is
                captured using first- and third-party cookies and other tracking technologies. For more information about
                how Microsoft collects and uses your data, visit the <a class="p-0"
                    href="https://privacy.microsoft.com/en-us/privacystatement" rel="noopener" target="_blank">Microsoft
                    Privacy Statement</a>.</p>

            <h3 id="analytics">Google Analytics</h3>
            <p>We use Google Analytics, a web analytics service provided by Google, to collect and analyze information about
                how users interact with our website. Google Analytics uses cookies to collect data such as the number of
                users, session statistics, approximate geolocation, and browser information. This data helps us understand
                website usage trends and improve our site. For more details on how Google Analytics collects and processes
                data, please refer to Google's Privacy Policy at <a class="p-0" href="https://policies.google.com/privacy"
                    rel="noopener" target="_blank">https://policies.google.com/privacy</a>. You can opt out of Google
                Analytics tracking by installing the Google Analytics Opt-out Browser Add-on, available at <a class="p-0"
                    href="https://tools.google.com/dlpage/gaoptout" rel="noopener"
                    target="_blank">https://tools.google.com/dlpage/gaoptout</a>.</p>
        </article>

        <article>
            <h2 id="sharing">Sharing Your Information</h2>
            <p>We do not sell your data. We share information only with the third-party providers mentioned above (Microsoft
                and Google) for the purposes described. These providers may store data on servers outside your country,
                subject to their privacy practices.</p>
        </article>

        <article>
            <h2 id="rights">Your Rights and Choices</h2>
            <p>Depending on your location, you may have rights to access, correct, or delete your data. To exercise these
                rights or opt out of tracking, contact me at <a class="p-0" href="mailto:support@luane.online">support@luane.online</a>. You can also:</p>
            <ul>
                <li>Opt out of Google Analytics as described above.</li>
                <li>For Microsoft Clarity, refer to the Microsoft Privacy Statement for options.</li>
            </ul>
        </article>

        <article>
            <h2 id="security">Security</h2>
            <p>We take reasonable measures to protect data, but no method is 100% secure. We encourage you to use caution
                when sharing information online.</p>
        </article>

        <article>
            <h2 id="changes">Changes to This Policy</h2>
            <p>We may update this policy periodically. Changes will be posted here with the updated date.</p>
        </article>

        <article>
            <h2 id="contact">Contact Me</h2>
            <p>If you have questions, contact me at <a class="p-0" href="mailto:support@luane.online">support@luane.online</a>.</p>
        </article>
    </section>
@endsection
