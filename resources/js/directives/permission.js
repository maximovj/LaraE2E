export default {
    mounted(el, binding) {
        const user = props.initialPage.props.auth.user;

        const hasPermission = (permissionName) => {
            return user?.permissions?.some(perm => perm.name === permissionName) ||
                user?.roles?.some(role => role.permissions?.some(perm => perm.name === permissionName));
        };

        const requiredPermissions = Array.isArray(binding.value)
            ? binding.value
            : [binding.value];

        if (!requiredPermissions.some(perm => hasPermission(perm))) {
            el.remove();
        }
    }
}
