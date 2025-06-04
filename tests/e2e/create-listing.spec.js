import { test, expect } from '@playwright/test';

test.describe('listing creation', () => {
  const loginPath = '/login';
  const createPath = '/listings/create';

  test('user can create a listing', async ({ page }) => {
    await page.goto(loginPath);
    await page.fill('input[name="email"]', 'maria@test.com');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');
    await page.waitForLoadState('networkidle');

    await page.goto(createPath);
    await page.fill('input[name="title"]', 'Test Listing');
    await page.fill('textarea[name="description"]', 'Test description');
    await page.fill('input[name="address"]', '123 Main St');
    await page.fill('input[name="city"]', 'Valencia');
    await page.fill('input[name="price"]', '500');
    await page.selectOption('select[name="type"]', 'apartamento');
    await page.fill('input[name="bedrooms"]', '2');
    await page.fill('input[name="bathrooms"]', '1');
    const today = new Date().toISOString().split('T')[0];
    await page.fill('input[name="available_from"]', today);
    await page.click('button[type="submit"]');

    await expect(page).toHaveURL(/\/listings\/\d+/);
    await expect(page.locator('body')).toContainText('Test Listing');
  });
});
