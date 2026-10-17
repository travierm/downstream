# Downstream Frontend

The frontend uses Vue 3, Vuetify 4, Vue Router 5, Vuex 4, and Vite.
Use Node.js 22.13 or newer and pnpm 11.20.0.

```sh
npm install --global pnpm@11.20.0
pnpm install --frozen-lockfile
cp .env.example .env.local
pnpm dev
```

Set the public API URL and Turnstile site key in `.env.local` as described in
[the setup guide](../setup.md). Production builds use `.env.production`.

```sh
pnpm build        # production build; warnings fail the command
pnpm serve        # preview the production build
pnpm test:unit    # Vitest
pnpm exec playwright install chromium
pnpm test:smoke   # build and run Playwright against the production preview
```

`pnpm test:e2e` runs Playwright against an existing build. Its fixtures intercept
API and external requests, so these checks do not require credentials or a live
backend. They cover public routes, login validation, search and collection
controls, mobile navigation, playback state, playlists, and charts.

To use an installed Chrome browser locally, set `PLAYWRIGHT_CHANNEL=chrome`.
CI installs Playwright's Chromium and runs the build, unit tests, and browser tests.

The root package provides wrappers such as `pnpm dev`, `pnpm build`, and
`pnpm test:smoke`; install the frontend dependencies in this directory first.
