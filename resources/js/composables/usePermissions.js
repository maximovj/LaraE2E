import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export function usePermissions() {
    const auth = computed(() => usePage().props.auth);

    const hasRole = (roleName) => {
        // Verifica si el rol existe en el array de roles del usuario
        return auth.value.user?.roles?.includes(roleName);
    };

    const hasPermission = (permissionName) => {
        // Verifica si el permiso existe directamente en el usuario o a través de sus roles
        return auth.value.user?.permissions?.includes(permissionName);
    };

    // Función adicional para verificar múltiples roles
    const hasAnyRole = (...roles) => {
        return roles.some(role => hasRole(role));
    };

    // Función adicional para verificar múltiples permisos
    const hasAnyPermission = (...permissions) => {
        return permissions.some(perm => hasPermission(perm));
    };

    // Función para verificar todos los roles (AND lógico)
    const hasAllRoles = (...roles) => {
        return roles.every(role => hasRole(role));
    };

    // Función para verificar todos los permisos (AND lógico)
    const hasAllPermissions = (...permissions) => {
        return permissions.every(perm => hasPermission(perm));
    };

    return {
        hasRole,
        hasPermission,
        hasAnyRole,
        hasAnyPermission,
        hasAllRoles,
        hasAllPermissions,
        // Propiedades computadas útiles
        currentRoles: computed(() => auth.value.user?.roles || []),
        currentPermissions: computed(() => auth.value.user?.permissions || [])
    };
}
