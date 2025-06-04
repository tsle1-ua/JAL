import { test, expect } from '@playwright/test';

test.describe('registration form responsive', () => {
  const registerPath = '/register';

  test('desktop layout', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 720 });
    await page.goto(registerPath);
    await expect(page.locator('form')).toBeVisible();
    await expect(page.locator('input[name="email"]')).toBeVisible();
  });

  test('mobile layout', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 812 });
    await page.goto(registerPath);
    await expect(page.locator('form')).toBeVisible();
    const emailInput = page.locator('input[name="email"]');
    await expect(emailInput).toBeVisible();
    const width = await emailInput.boundingBox();
    expect(width?.width).toBeLessThanOrEqual(375);
  });
});
