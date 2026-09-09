import fs from 'node:fs';
import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin';

const modulesPath = 'app-modules';

/**
 * Точки входа модулей подхватываются автоматически: module:make создаёт
 * resources/assets/{css,js}, отдельная правка этого файла не нужна.
 */
const moduleEntrypoints = (fs.existsSync(modulesPath) ? fs.readdirSync(modulesPath) : [])
    .flatMap((module) => [
        `${modulesPath}/${module}/resources/assets/css/app.css`,
        `${modulesPath}/${module}/resources/assets/js/app.js`,
    ])
    .filter((entrypoint) => fs.existsSync(entrypoint));

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                ...moduleEntrypoints,
            ],
            refresh: [
                ...refreshPaths,
                `${modulesPath}/*/resources/views/**`,
                `${modulesPath}/*/routes/**`,
            ],
        }),
    ],
});
