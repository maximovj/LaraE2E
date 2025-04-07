export default {
    mounted(el, binding, vnode) {
        const user = vnode.appContext.config.globalProperties.$page.props.auth.user;

        const hasRole = (roleName) => {
            return user?.roles?.some(role => role.name === roleName);
        };

        const requiredRoles = Array.isArray(binding.value)
            ? binding.value
            : [binding.value];

        if (!requiredRoles.some(role => hasRole(role))) {
            el.remove();
        }
    }
};
