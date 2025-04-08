import { ref, computed, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';

// Cache en memoria
const rolesCache = ref(new Set());
const permissionsCache = ref(new Set());

export function useAuthCache() {
    const auth = computed(() => usePage().props?.auth ?? {
        user: {
            id: null,
            name: null,
            email: null,
            roles: [],
            permissions: [],
        }
    }); // ✅ Nunca undefined

    const getRoles = () => {
        if (rolesCache.value.size === 0 && auth.value.user?.roles) {
            rolesCache.value = new Set(auth.value.user.roles);
        }
        return rolesCache.value;
    };

    const getPermissions = () => {
        if (permissionsCache.value.size === 0 && auth.value.user?.permissions) {
            permissionsCache.value = new Set(auth.value.user.permissions);
        }
        return permissionsCache.value;
    };

    // Watcher para limpiar cache cuando cambie el usuario
    watch(
        () => auth.value.user?.id,
        (newId, oldId) => {
            if (newId !== oldId) {
                rolesCache.value.clear();
                permissionsCache.value.clear();
            }
        },
        { immediate: true }
    );

    const hasRole = (roleName) => {
        return getRoles().has(roleName);
    };

    const hasPermission = (permissionName) => {
        return getPermissions().has(permissionName);
    };

    const clearCache = () => {
        rolesCache.value.clear();
        permissionsCache.value.clear();
    };

    return {
        hasRole,
        hasPermission,
        clearCache,
        // Métodos adicionales útiles
        getCurrentRoles: () => Array.from(getRoles()),
        getCurrentPermissions: () => Array.from(getPermissions())
    };
}
