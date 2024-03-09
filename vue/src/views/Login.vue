<template>
  <div class="mxa mt12" style="width: 350px">
    <Form @submit="handleLogin" :validation-schema="schema">
      <div class="form-group">
        <label class="title4" for="email">Email</label>
        <Field name="email" type="email" class="form-control" />
        <ErrorMessage name="email" class="error-feedback" />
      </div>

      <div class="form-group">
        <label class="title4" for="password">Password</label>
        <Field name="password" type="password" class="form-control" />
        <ErrorMessage name="password" class="error-feedback" />
      </div>

      <w-button type="submit" class="fill-width" :loading="loading" :disabled="loading" xl>
        Login
      </w-button>

      <div class="form-group">
        <div v-if="message" class="alert alert-danger" role="alert">
          {{ message }}
        </div>
      </div>
    </Form>
  </div>
</template>

<script>
import { Form, Field, ErrorMessage } from "vee-validate";
import * as yup from "yup";

export default {
  name: "Login",
  components: {
    Form,
    Field,
    ErrorMessage,
  },
  data() {
    const schema = yup.object().shape({
      email: yup.string().email('Email is invalid').required("Email is required"),
      password: yup.string().required("Password is required"),
    });

    return {
      loading: false,
      message: "",
      schema,
    };
  },
  computed: {
    loggedIn() {
      return this.$store.state.auth.status.loggedIn;
    },
  },
  created() {
    if (this.loggedIn) {
      this.$router.push("/movies");
    }
  },
  methods: {
    handleLogin(user) {
      this.loading = true;

      this.$store.dispatch("auth/login", user).then(
        () => {
          this.$router.push("/movies");
        },
        (error) => {
          this.loading = false;
          this.message =
            (error.response &&
              error.response.data &&
              error.response.data.message) ||
            error.message ||
            error.toString();
        }
      );
    },
  },
};
</script>