import './bootstrap';
import '@vuepic/vue-datepicker/dist/main.css';
import '/node_modules/flag-icons/css/flag-icons.min.css';
import '/node_modules/vue3-treeview/dist/style.css';
import 'vue-json-pretty/lib/styles.css';
import '../css/app.css';

import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/inertia-vue3';
import { InertiaProgress } from '@inertiajs/progress';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/vue.m';
import translationPlugin from './translation';

import { TagsInput } from '@nor1c/vue-tagsinput';
import Tree from 'vue3-treeview';
import Vue3ChartJs from '@j-t-mcc/vue3-chartjs';
import VueApexCharts from "vue3-apexcharts";
import NumberAbbreviate from 'number-abbreviate';
import Datepicker from '@vuepic/vue-datepicker';
import VueJsonPretty from 'vue-json-pretty';

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, app, props, plugin }) {
        const myApp = createApp({ render: () => h(app, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(translationPlugin)
            .use(VueApexCharts)
            .component('BaseTagsInput', TagsInput)
            .component('Treeview', Tree)
            .component('ChartJs', Vue3ChartJs)
            .component('Datepicker', Datepicker)
            .component('JsonPretty', VueJsonPretty);

        myApp.provide('abbr', NumberAbbreviate)

        myApp.mount(el);
        return myApp;
    },
});

InertiaProgress.init({ color: '#fa4616' });

// Pusher.log = function (message) { window.console.log(message); }
