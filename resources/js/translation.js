import { usePage } from '@inertiajs/inertia-vue3'

export default {
    install: (app) => {

        app.config.globalProperties.__t = (key, replacements = {}) => {
            let translation = ""


            translation = usePage().props.value.translations[key] || key;

            Object.keys(replacements).forEach(r => {
                translation = translation.replace(`:${r}`, replacements[r]);
            });
            return translation;

        }
        const r = (key, replacements = {}) => {
            let translation = ""


            translation = usePage().props.value.translations[key] || key;

            Object.keys(replacements).forEach(r => {
                translation = translation.replace(`:${r}`, replacements[r]);
            });
            return translation;

        }
        app.mixin({
            methods: {
                __t: r,
                __: r,
            },
        });
        app.__t = r;
        // app.prototype.$__t = r;
        app.provide('translate', r)
        app.provide('__t', r)
    }
}
