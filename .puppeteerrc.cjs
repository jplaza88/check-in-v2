const { join } = require('node:path');

/**
 * Keep the browser inside the project rather than $HOME/.cache/puppeteer.
 *
 * The default location lives in each container's own filesystem, so Chrome was
 * only ever present in the `checkin` container, was destroyed by any rebuild,
 * and the `horizon` container never had it at all -- which is where the queued
 * PDF email is rendered. This directory is inside the bind mount, so one copy
 * is shared by every container and survives a rebuild.
 *
 * @type {import('puppeteer').Configuration}
 */
module.exports = {
    cacheDirectory: join(__dirname, '.puppeteer'),
};
