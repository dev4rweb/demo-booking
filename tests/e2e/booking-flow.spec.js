import { test, expect } from '@playwright/test';

test('guest can complete the public booking flow', async ({ page }) => {
  await page.goto('/b/luna-beauty');
  await expect(page.getByRole('heading', { name: 'Luna Beauty Studio' })).toBeVisible();

  await page.getByRole('button', { name: /Signature haircut/i }).click();
  await page.getByRole('button', { name: /Any specialist|Olena/i }).first().click();

  const dateInput = page.locator('input[type="date"]');
  await expect(dateInput).toBeVisible();

  const today = new Date();
  let slotted = false;
  for (let i = 1; i <= 14; i++) {
    const d = new Date(today);
    d.setDate(today.getDate() + i);
    if (d.getDay() === 0) continue;
    const iso = `${d.getFullYear()}-${String(d.getMonth() + 1).padStart(2, '0')}-${String(d.getDate()).padStart(2, '0')}`;
    await dateInput.fill(iso);
    await dateInput.dispatchEvent('change');
    const slot = page.locator('section button').filter({ hasText: /:/ }).first();
    try {
      await slot.waitFor({ timeout: 4000 });
      await slot.click();
      slotted = true;
      break;
    } catch {
      // try next day
    }
  }

  expect(slotted).toBeTruthy();
  await page.locator('#guest-name').fill('Sofia Pavlenko');
  await page.locator('#guest-phone').fill('+380662223344');
  await page.locator('#guest-email').fill('sofia.e2e@example.com');
  await page.getByRole('button', { name: /confirm booking/i }).click();
  await expect(page.getByRole('heading', { name: /on the calendar/i })).toBeVisible({ timeout: 20000 });
});
