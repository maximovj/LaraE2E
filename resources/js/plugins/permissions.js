export default {
    install(app) {
        app.config.globalProperties.$hasRole = function (roleName) {
            const user = this.$page.props.auth.user;
            return user?.roles?.some(role => role.name === roleName);
        };

        app.config.globalProperties.$hasPermission = function (permissionName) {
            const user = this.$page.props.auth.user;
            return user?.permissions?.some(perm => perm.name === permissionName) ||
                user?.roles?.some(role => role.permissions.some(perm => perm.name === permissionName));
        };
    }
};
