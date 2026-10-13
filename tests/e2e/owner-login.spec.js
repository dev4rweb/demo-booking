import { test, expect } from '@playwright/test';

test('owner can log in and open the dashboard', async ({ page }) => {
  await page.goto('/login');
  await page.fill('input#email', 'owner@demo.local');
  await page.fill('input#password', 'demo123456');
  await page.getByRole('button', { name: /log in/i }).click();
  await page.waitForURL(/dashboard/, { timeout: 15000 });
  await expect(page.getByRole('heading', { name: /Dashboard/i })).toBeVisible();
});
