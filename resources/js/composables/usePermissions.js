import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function usePermissions() {
    const user = computed(() => usePage().props.auth.user);

    const hasRole = (roleName) => {
        return user.value?.roles?.some(role => role.name === roleName);
    };

    const hasPermission = (permissionName) => {
        return user.value?.permissions?.some(perm => perm.name === permissionName) ||
            user.value?.roles?.some(role => role.permissions.some(perm => perm.name === permissionName));
    };

    return { hasRole, hasPermission };
}
