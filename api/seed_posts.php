<?php
// Starter blog posts. Editable or removable in the admin dashboard.
return [
  ['title' => 'PHP 8.3: the features that matter for everyday web projects', 'category' => 'Software', 'summary' => 'A practical look at typed class constants, the #[Override] attribute, json_validate() and other changes in PHP 8.3.', 'featured' => 0, 'cover' => 'assets/blog-1.jpg',
   'body' => 'PHP 8.3 was released in November 2023 and is a solid step forward for teams that build school portals, business systems and APIs in PHP.

Typed class constants let you declare the type of a constant, so a value that should always be a string or an integer is checked by the engine instead of by convention. The #[Override] attribute lets you mark a method that is meant to override a parent method. If the parent method is later renamed or removed, PHP raises an error instead of silently leaving dead code behind.

json_validate() checks whether a string is valid JSON without decoding it into memory, which is useful when you accept large payloads from webhooks or APIs. Dynamic class constant fetch (Foo::{$name}) removes a few awkward workarounds, and additions to the Randomizer class make it easier to generate random strings and numbers in a controlled way.

For existing applications the practical advice is simple: test on a staging copy, read the deprecation notices, and upgrade framework dependencies first. Running a supported PHP version also matters for security, because older branches stop receiving fixes. We build our Laravel projects on current PHP releases for that reason.'],
  ['title' => 'Laravel 12: a calm release with new starter kits', 'category' => 'Software', 'summary' => 'Laravel 12 focused on stability and new application starter kits for React, Vue and Livewire.', 'featured' => 0, 'cover' => 'assets/blog-2.jpg',
   'body' => 'Laravel 12 arrived in February 2025 and, unlike some earlier major versions, it was designed to be easy to adopt. The team kept breaking changes to a minimum and concentrated on updating upstream dependencies.

The headline change is a new set of application starter kits. New projects can start with a React, Vue or Livewire frontend, with authentication screens ready to use. There is also an option based on WorkOS AuthKit that adds social sign-in, passkeys and single sign-on. Older starter packages such as Breeze and Jetstream are no longer receiving new features.

For agencies like ours the benefit is speed and consistency. A starter kit gives a new project sensible defaults for login, registration and password reset, so developer time goes into the parts that make a system useful, such as result processing, fee records or reporting.

If you maintain an older Laravel application, plan upgrades one major version at a time, read the official upgrade guide, and run your test suite before and after. Laravel continues to release new major versions, so check the official release notes for the current version before you start.'],
  ['title' => 'React 19 and Next.js 15: what changed for web teams', 'category' => 'Web', 'summary' => 'React 19 brought Actions and new hooks, while Next.js 15 changed caching defaults and made request APIs asynchronous.', 'featured' => 0, 'cover' => 'assets/blog-3.jpg',
   'body' => 'React 19 became stable in December 2024. Its main idea is Actions: functions, often asynchronous, that handle form submissions and updates while React manages pending states and errors for you. New hooks such as useActionState and useOptimistic support this style, and the use API can read a promise or context during rendering. Function components can also receive a ref as an ordinary prop, which removes most uses of forwardRef.

Next.js 15, released in October 2024, added support for React 19 and made several defaults more predictable. Request-specific APIs such as cookies and headers became asynchronous, and fetch requests and GET route handlers are no longer cached by default. Turbopack for local development was marked stable.

For client projects these changes mean cleaner form handling and fewer surprises with stale data. They also mean upgrades need care: async request APIs touch many files, and code that relied on the old caching behaviour should be reviewed.

Our advice is to upgrade on a branch, run the official codemods where they exist, test the pages that read cookies or fetch data, and deploy to a staging environment before production.'],
  ['title' => 'PostgreSQL 17 and 18: faster, easier to operate', 'category' => 'Databases', 'summary' => 'Incremental backups, JSON_TABLE, MERGE with RETURNING, asynchronous I/O and UUIDv7 are among the changes in recent PostgreSQL releases.', 'featured' => 0, 'cover' => 'assets/blog-4.jpg',
   'body' => 'PostgreSQL is a database we use for several of our applications, and recent releases add features that help real projects.

PostgreSQL 17, released in September 2024, added incremental backups to pg_basebackup, which can reduce backup time and storage for larger databases. It also introduced JSON_TABLE for turning JSON data into rows, let MERGE return the affected rows with RETURNING, and improved the memory use of vacuum.

PostgreSQL 18, released in September 2025, added an asynchronous I/O subsystem that can speed up some read-heavy workloads, a uuidv7() function that generates time-ordered identifiers, virtual generated columns, and support for OAuth authentication. Time-ordered UUIDs are attractive because they keep indexes tidier than random UUIDs.

None of this means you should upgrade in a hurry. Managed providers decide which versions they offer, and a major upgrade needs testing, a backup and a rollback plan. What it does mean is that PostgreSQL keeps getting better at the unglamorous work of backups, performance and security, which is exactly what business systems depend on.'],
  ['title' => 'The OWASP Top 10: security basics every school system should cover', 'category' => 'Security', 'summary' => 'The OWASP Top 10 lists the most common web application risks. Here is how they apply to systems that hold student records.', 'featured' => 0, 'cover' => 'assets/blog-5.jpg',
   'body' => 'School systems hold names, results, fee records and contact details, so they deserve proper security. The OWASP Top 10 is a widely used list of the most common web application risks. The 2021 edition starts with broken access control, followed by cryptographic failures, injection, insecure design, security misconfiguration, vulnerable and outdated components, identification and authentication failures, software and data integrity failures, logging and monitoring failures, and server-side request forgery.

Broken access control matters most in practice. A student or parent must only ever see their own results, and the server, not the browser, must enforce that. Use role checks on every request and test them with real accounts.

Injection is prevented by using prepared statements for all database queries. Passwords must be hashed with a modern algorithm, never stored in plain text. Login forms should be rate limited, secrets must stay out of source code, and dependencies should be updated regularly.

Finally, keep logs of important actions such as publishing results or changing fees, so problems can be traced. OWASP updates its list from time to time, so check owasp.org for the current edition. These measures are not exotic, but together they prevent most of the incidents we see.'],
  ['title' => 'Passkeys: signing in without passwords', 'category' => 'Security', 'summary' => 'Passkeys replace passwords with cryptographic keys that stay on your device and resist phishing.', 'featured' => 0, 'cover' => 'assets/blog-6.jpg',
   'body' => 'Passwords are easy to guess, reuse and steal. Passkeys are designed to fix that. They are built on the FIDO2 and WebAuthn standards and use public-key cryptography: the website stores a public key, while the private key stays on your phone, computer or security key and never leaves it.

When you sign in, your device asks you to confirm with a fingerprint, face scan or device PIN, and then proves possession of the private key. Because the credential is tied to the real website address, a fake login page cannot capture something it can reuse, which makes passkeys resistant to phishing.

Apple, Google and Microsoft support passkeys in their platforms and major browsers, and many passkeys can sync across a user\'s devices through their account.

For developers the sensible approach is gradual. Offer passkeys as an additional sign-in method alongside passwords, keep a safe account recovery process, and test on the devices your users actually own. Frameworks and authentication services increasingly include ready-made support, so adding passkeys is more achievable than it was a few years ago.'],
  ['title' => 'Nigeria\'s Data Protection Act 2023: what it means for software projects', 'category' => 'Business', 'summary' => 'The Act sets out principles for handling personal data. Here is what teams building websites and portals should keep in mind.', 'featured' => 0, 'cover' => 'assets/blog-7.jpg',
   'body' => 'The Nigeria Data Protection Act 2023 gives legal backing to the Nigeria Data Protection Commission and sets rules for organisations that collect and use personal data. Schools, businesses and other organisations that run websites or portals are affected.

In general terms, personal data should be collected for specific, lawful purposes, limited to what is needed, kept accurate, stored securely and not kept longer than necessary. People have rights over their data, including the right to access it, ask for corrections and object to certain uses. Organisations must also report certain data breaches to the Commission within 72 hours of becoming aware of them.

For software teams this turns into practical design choices: ask only for the fields you need, explain clearly why you collect them, restrict access by role, log who changed what, protect data in transit and at rest, and plan how to export or delete a person\'s records when required.

This article is a general overview and not legal advice. Organisations with specific questions should read the Act and seek advice from a qualified Nigerian lawyer or data protection professional.'],
  ['title' => 'Core Web Vitals: what INP changed', 'category' => 'Web', 'summary' => 'Interaction to Next Paint replaced First Input Delay in 2024. Here is what the three Core Web Vitals measure and how to improve them.', 'featured' => 0, 'cover' => 'assets/blog-8.jpg',
   'body' => 'Google uses Core Web Vitals to describe how real visitors experience a page. In March 2024, Interaction to Next Paint (INP) replaced First Input Delay as the responsiveness metric. The three current metrics are Largest Contentful Paint (LCP), INP and Cumulative Layout Shift (CLS).

LCP measures how quickly the main content appears, and a good score is 2.5 seconds or less. INP measures how quickly the page responds to taps, clicks and key presses throughout a visit, and a good score is 200 milliseconds or less. CLS measures unexpected movement of content, and a good score is 0.1 or less. These are judged at the 75th percentile of real visits.

Practical improvements are usually simple. Serve images in modern formats such as WebP at the size they are displayed, and set width and height so the layout does not jump. Load only the JavaScript a page needs, and avoid long tasks that block the main thread. Use a fast hosting region and caching.

On mobile networks these steps matter even more, so we test our websites on real phones, not only on desktop computers.'],
  ['title' => 'AI coding assistants: where they help and where they need supervision', 'category' => 'AI', 'summary' => 'AI tools can speed up routine development work, but the developer remains responsible for the result.', 'featured' => 1, 'cover' => 'assets/blog-9.jpg',
   'body' => 'AI coding assistants are now part of many developers\' daily tools. They are good at routine work: drafting boilerplate, suggesting tests, explaining unfamiliar code, converting data formats and proposing refactors.

They also have clear limits. Generated code can look correct and still be wrong. It may use outdated library functions, miss edge cases, or introduce security problems such as missing access checks or unsafe queries. Tools can also suggest code whose licence is unclear, and developers may be tempted to paste confidential material into a prompt.

Good habits reduce the risk. Treat suggestions like code from a new colleague: read it, run it and write tests for it. Run linters and security scanners as usual. Never paste passwords, API keys or private customer data into a tool unless your organisation has approved it. Keep changes small so reviews stay meaningful, and make sure a human understands and owns every line that is shipped.

Used this way, AI assistance can save time on repetitive tasks and leave more room for design, testing and talking to clients, which is where software projects are won or lost.'],
  ['title' => 'Payment webhooks done right: signatures, idempotency and verification', 'category' => 'Software', 'summary' => 'How to handle Paystack and Flutterwave style webhooks safely, so every payment is recorded once and only when it is genuine.', 'featured' => 0, 'cover' => 'assets/blog-10.jpg',
   'body' => 'When a customer pays through a gateway such as Paystack or Flutterwave, your system usually learns about the result through a webhook: a message the gateway sends to a URL you provide. Handling it carelessly can lead to missed payments, duplicate receipts or fake confirmations.

First, verify that the message is genuine. Paystack signs each event with an HMAC of the request body, sent in the x-paystack-signature header and computed with your secret key. Flutterwave sends a verif-hash header that you compare with the secret hash you configured. Reject anything that does not match.

Second, confirm the transaction with the gateway\'s verification endpoint before you give value, such as marking a fee as paid or releasing a result. Check the reference, amount and currency.

Third, make processing idempotent. Gateways can send the same event more than once, so store each transaction reference and ignore repeats. Respond quickly with a success status and do slow work in the background.

Finally, log events and reconcile your records against the gateway dashboard regularly. These steps are the foundation of the payment flows we build into school and business systems.'],
];
