import { test } from '@playwright/test';
import fs from 'fs';
import path from 'path';

const widths = [320, 768, 1440];
const themes = ['dark', 'light'];
const artifactDir = process.env.SCREENSHOT_DIR || '/opt/cursor/artifacts/screenshots';

async function setTheme(page, theme) {
  await page.addInitScript((value) => {
    localStorage.setItem('theme', value);
    document.documentElement.classList.remove('dark', 'light');
    document.documentElement.classList.add(value);
    document.documentElement.style.colorScheme = value;
  }, theme);
}

async function login(page, email = 'owner@demo.local') {
  await page.goto('/login');
  await page.fill('input#email', email);
  await page.fill('input#password', 'demo123456');
  await page.getByRole('button', { name: /log in/i }).click();
  await page.waitForURL(/dashboard|calendar|admin/, { timeout: 15000 });
}

async function shot(page, name) {
  fs.mkdirSync(artifactDir, { recursive: true });
  await page.waitForTimeout(250);
  await page.screenshot({
    path: path.join(artifactDir, `${name}.png`),
    fullPage: true,
  });
}

for (const theme of themes) {
  for (const width of widths) {
    test.describe(`${theme} ${width}px`, () => {
      test.use({ viewport: { width, height: 900 } });

      test.beforeEach(async ({ page }) => {
        await setTheme(page, theme);
      });

      test('landing', async ({ page }) => {
        await page.goto('/');
        await page.waitForSelector('h1');
        await shot(page, `landing-${theme}-${width}`);
      });

      test('about-en', async ({ page }) => {
        await page.goto('/about');
        await page.waitForSelector('h1');
        await shot(page, `about-en-${theme}-${width}`);
      });

      test('howto-en', async ({ page }) => {
        await page.goto('/how-to');
        await page.waitForSelector('h1');
        await shot(page, `howto-en-${theme}-${width}`);
      });

      test('howto-ar', async ({ page }) => {
        await page.goto('/ar/how-to');
        await page.waitForSelector('h1');
        await shot(page, `howto-ar-${theme}-${width}`);
      });

      test('booking flow steps', async ({ page }) => {
        await page.goto('/b/luna-beauty');
        await page.waitForSelector('h1');
        await shot(page, `booking-service-${theme}-${width}`);
        await page.getByRole('button', { name: /Signature haircut/i }).click();
        await shot(page, `booking-staff-${theme}-${width}`);
        await page.getByRole('button', { name: /Any specialist|Olena/i }).first().click();
        await page.waitForTimeout(500);
        await shot(page, `booking-slots-${theme}-${width}`);
      });

      test('owner dashboard and calendar', async ({ page }) => {
        await login(page);
        await shot(page, `owner-dashboard-${theme}-${width}`);
        await page.goto('/calendar?view=week');
        await page.waitForSelector('h2');
        await shot(page, `owner-calendar-${theme}-${width}`);
      });

      test('staff view', async ({ page }) => {
        await login(page, 'staff@demo.local');
        await page.goto('/calendar?view=day');
        await page.waitForSelector('h2');
        await shot(page, `staff-calendar-${theme}-${width}`);
      });
    });
  }
}

test('og-image owner dashboard dark 1200x630', async ({ page }) => {
  await setTheme(page, 'dark');
  await page.setViewportSize({ width: 1200, height: 630 });
  await login(page);
  await page.waitForTimeout(400);
  const dest = path.join(process.cwd(), 'public/og-image.jpg');
  await page.screenshot({ path: dest, type: 'jpeg', quality: 82, fullPage: false });
  fs.mkdirSync(artifactDir, { recursive: true });
  fs.copyFileSync(dest, path.join(artifactDir, 'og-image.jpg'));
});
