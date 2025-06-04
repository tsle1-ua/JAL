import { test, expect } from '@playwright/test';

test.describe('navigation responsive', () => {
  const homePath = '/';

  test('desktop navigation visible', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 720 });
    await page.goto(homePath);
    const navLinks = page.locator('nav .navbar-nav .nav-link');
    await expect(navLinks.first()).toBeVisible();
  });

  test('mobile navigation collapses', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 812 });
    await page.goto(homePath);
    const toggler = page.locator('.navbar-toggler');
    await expect(toggler).toBeVisible();
    await toggler.click();
    const navMenu = page.locator('#navbarSupportedContent');
    await expect(navMenu).toBeVisible();
  });
});
