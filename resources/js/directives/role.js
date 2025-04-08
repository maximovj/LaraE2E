import { useAuthCache } from '@/composables/useAuthCache';

export default {
    mounted(el, binding) {
        const { hasRole } = useAuthCache();

        const requiredRoles = Array.isArray(binding.value) ? binding.value : [binding.value];

        if (!requiredRoles.some(hasRole)) {
            el.remove();
        }
    }
};
