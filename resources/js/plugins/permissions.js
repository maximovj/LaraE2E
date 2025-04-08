import { useAuthCache } from '@/composables/useAuthCache';

export default {
    install(app) {
        const { hasRole, hasPermission } = useAuthCache();

        app.config.globalProperties.$hasRole = hasRole;
        app.config.globalProperties.$hasPermission = hasPermission;
    }
};
