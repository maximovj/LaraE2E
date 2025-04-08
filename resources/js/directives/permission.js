import { useAuthCache } from '@/composables/useAuthCache';

export default {
    mounted(el, binding) {
        const { hasPermission } = useAuthCache(); // Usamos la caché de autenticación

        // Obtener los permisos requeridos desde el binding
        const requiredPermissions = Array.isArray(binding.value) ? binding.value : [binding.value];

        // Si el usuario no tiene al menos un permiso, eliminar el elemento
        if (!requiredPermissions.some(perm => hasPermission(perm))) {
            el.remove();
        }
    }
};
