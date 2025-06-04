/** @type {import('@playwright/test').PlaywrightTestConfig} */
const config = {
  testDir: './tests/e2e',
  retries: 0,
  use: {
    baseURL: 'http://localhost:8000',
    trace: 'on-first-retry'
  },
};

export default config;
