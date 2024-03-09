<template>
  <div id="app">
    <w-toolbar shadow>
      <div class="title2">Coding Test</div>
      <div class="spacer"></div>
      <w-button color="primary" class="ml2" text md v-if="!currentUser" route="/login">
        Login
      </w-button>
      <w-button color="primary" class="ml2" text md v-if="!currentUser" route="/register">
        Register
      </w-button>
      <w-button color="primary" class="ml2" text md v-if="currentUser" route="/movies">
        Movies
      </w-button>

      <w-menu 
        v-if="currentUser" 
        shadow 
        align-right 
        min-width="250">
        <template #activator="{ on }">
          <w-button
            v-on="on"
            icon="fa fa-user-circle-o"
            xl
            class="ml4">
          </w-button>
        </template>

        <w-card content-class="pa0">
          <w-toolbar>
            <div class="title4 ma0">User Profile</div>
            <div class="spacer"></div>
            <w-button color="primary" class="ml2" text md route="/user">
              Edit
            </w-button>
            <w-button color="primary" class="ml2" text md @click.prevent="logOut">
              Logout
            </w-button>
          </w-toolbar>
          <div class="pa3">
            <div class="title4">{{ currentUser.user.name }}</div>
            <div class="title5">{{ currentUser.user.email }}</div>
          </div>
        </w-card>
      </w-menu>
    </w-toolbar>

    <div class="spacer"></div>

    <div>
      <router-view />
    </div>
  </div>
</template>

<script>
export default {
  data: () => ({
    showMenu: false
  }),
  computed: {
    currentUser() {
      return this.$store.state.auth.user;
    },
  },
  methods: {
    logOut() {
      this.$store.dispatch('auth/logout');
      this.$router.push('/login');
    }
  }
};
</script>