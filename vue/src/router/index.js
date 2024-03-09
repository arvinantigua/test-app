import { createRouter, createWebHistory } from 'vue-router'
import Login from '../views/Login.vue';
import Register from "../views/Register.vue";

const Movies = () => import("../views/Movies.vue");
const Profile = () => import("../views/Profile.vue");

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/login',
      name: 'login',
      component: Login
    },
    {
      path: '/register',
      name: 'register',
      component: Register
    },
    {
      path: '/movies',
      name: 'movies',
      component: Movies
    },
    {
      path: '/user',
      name: 'user',
      component: Profile
    },
  ]
});

router.beforeEach((to, from, next) => {
  const publicPages = ['/login', '/register'];
  const authRequired = !publicPages.includes(to.path);
  const loggedIn = localStorage.getItem('user');
  if (authRequired && !loggedIn) {
    next('/login');
  } else {
    next();
  }
});

export default router
