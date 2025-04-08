import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

export default {
    mounted(el, binding) {
        const auth = computed(() => usePage().props.auth);

        if (!auth.value?.user) {
            el.remove();
            return;
        }

        const requiredRoles = Array.isArray(binding.value) ? binding.value : [binding.value];

        const hasRole = (roleName) => auth.value.user.roles.includes(roleName);

        if (!requiredRoles.some(hasRole)) {
            el.remove();
        }
    }
};
