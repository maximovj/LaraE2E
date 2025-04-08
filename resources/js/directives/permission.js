import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export default {
    mounted(el, binding) {
        // Obtener el usuario autenticado de Inertia.js
        const user = computed(() => usePage().props.auth.user);

        // Función para verificar si el usuario tiene un permiso
        const hasPermission = (permissionName) => {
            return user.value?.permissions?.includes(permissionName) ||
                user.value?.roles?.some(role => role.permissions?.includes(permissionName));
        };

        // Obtener los permisos requeridos desde el binding
        const requiredPermissions = Array.isArray(binding.value) ? binding.value : [binding.value];

        // Si el usuario no tiene al menos un permiso, eliminar el elemento
        if (!requiredPermissions.some(perm => hasPermission(perm))) {
            el.remove();
        }
    }
};
