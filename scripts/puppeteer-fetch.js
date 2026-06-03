#!/usr/bin/env node
// fetcher.js
// Outputs full HTML to stdout. Errors to stderr and exits with non-zero code.

const puppeteer = require('puppeteer');

(async () => {
  try {
    const url = process.argv[2];
    if (!url) {
      console.error('Usage: node fetcher.js <url>');
      process.exit(1);
    }

    const browser = await puppeteer.launch({
      headless: true,
      args: ['--no-sandbox', '--disable-setuid-sandbox'],
    });

    const page = await browser.newPage();
    await page.setUserAgent('Mozilla/5.0 (compatible; Puppeteer Fetcher/1.0)');
    await page.setDefaultNavigationTimeout(30000); 

    try {
      await page.goto(url, { waitUntil: 'networkidle2', timeout: 30000 });
    } catch (navErr) {
      // try a fallback: load  and wait a short time
      try {
        await page.goto(url, { waitUntil: 'domcontentloaded', timeout: 30000 });
        await page.waitForTimeout(2000);
      } catch (e) {
        console.error('Navigation failed:', navErr.message);
        await browser.close();
        process.exit(1);
      }
    }

    // Optionally emulate scrolling to trigger lazy-loaded content
    await autoScroll(page);

    const html = await page.content();
    console.log(html);

    await browser.close();
    process.exit(0);

  } catch (err) {
    console.error('Fatal error:', err.message || err);
    process.exit(1);
  }
})();

async function autoScroll(page) {
  await page.evaluate(async () => {
    await new Promise(resolve => {
      let total = 0;
      const distance = 300;
      const timer = setInterval(() => {
        window.scrollBy(0, distance);
        total += distance;
        if (total >= document.body.scrollHeight - window.innerHeight) {
          clearInterval(timer);
          resolve();
        }
      }, 150);
    });
  });
}
