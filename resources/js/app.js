import '../css/app.css';
import './bootstrap';

import { createInertiaApp, router } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Bookly';

const applyDocumentLocale = (props) => {
    const locale = props?.locale || 'en';
    const dir = props?.dir || (locale === 'ar' ? 'rtl' : 'ltr');
    document.documentElement.lang = locale;
    document.documentElement.dir = dir;
};

createInertiaApp({
    title: (title) => (title ? `${title} - ${appName}` : appName),
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        applyDocumentLocale(props.initialPage?.props);

        router.on('navigate', (event) => {
            applyDocumentLocale(event.detail.page.props);
        });

        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
    },
    progress: {
        color: '#E8C07A',
    },
});
