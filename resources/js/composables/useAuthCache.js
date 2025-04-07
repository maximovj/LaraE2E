import { ref, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Cache en memoria
const rolesCache = ref(new Map());
const permissionsCache = ref(new Map());

export function useAuthCache() {
    const user = computed(() => usePage().props.auth.user);

    const getRoles = () => {
        if (!rolesCache.value.has('userRoles')) {
            rolesCache.value.set('userRoles', new Set(user.value?.roles?.map(role => role.name) || []));
        }
        return rolesCache.value.get('userRoles');
    };

    const getPermissions = () => {
        if (!permissionsCache.value.has('userPermissions')) {
            const directPermissions = new Set(user.value?.permissions?.map(p => p.name) || []);
            const rolePermissions = new Set(
                user.value?.roles?.flatMap(role =>
                    role.permissions?.map(p => p.name) || []
                ) || []
            );

            const allPermissions = new Set([...directPermissions, ...rolePermissions]);
            permissionsCache.value.set('userPermissions', allPermissions);
        }
        return permissionsCache.value.get('userPermissions');
    };

    // Watcher para limpiar cache cuando cambie el usuario
    watch(
        () => user.value?.id,
        (newId, oldId) => {
            if (newId !== oldId) {
                rolesCache.value.clear();
                permissionsCache.value.clear();
            }
        },
        { immediate: true } // Se ejecuta inmediatamente al montar
    );

    const hasRole = (roleName) => {
        return getRoles().has(roleName);
    };

    const hasPermission = (permissionName) => {
        return getPermissions().has(permissionName);
    };

    // Limpiar cache cuando cambie el usuario (útil en apps SPA)
    const clearCache = () => {
        rolesCache.value.clear();
        permissionsCache.value.clear();
    };

    return {
        hasRole,
        hasPermission,
        clearCache
    };
}
