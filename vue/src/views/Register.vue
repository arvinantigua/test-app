<template>
  <div class="mxa mt12" style="width: 350px">
      <Form @submit="handleRegister" :validation-schema="schema">
        <div v-if="!successful">
          <div class="form-group">
            <label for="name">Name</label>
            <Field name="name" type="text" class="form-control" />
            <ErrorMessage name="name" class="error-feedback" />
          </div>
          <div class="form-group">
            <label for="email">Email</label>
            <Field name="email" type="email" class="form-control" />
            <ErrorMessage name="email" class="error-feedback" />
          </div>
          <div class="form-group">
            <label for="password">Password</label>
            <Field name="password" type="password" class="form-control" />
            <ErrorMessage name="password" class="error-feedback" />
          </div>

          <w-button type="submit" class="fill-width" :loading="loading" :disabled="loading" xl>
	         Register
	       </w-button>
	    </div>
      </Form>

      <w-button v-if="message" class="mt2" :bg-color="successful ? 'success' : 'error'">
      	{{ message }}
      </w-button>
  </div>
</template>

<script>
import { Form, Field, ErrorMessage } from "vee-validate";
import * as yup from "yup";

export default {
  name: "Register",
  components: {
    Form,
    Field,
    ErrorMessage,
  },
  data() {
    const schema = yup.object().shape({
      name: yup
        .string()
        .required("Name is required")
        .min(3, "Must be at least 3 characters")
        .max(30, "Must be maximum 30 characters"),
      email: yup
        .string()
        .required("Email is required")
        .email("Email is invalid")
        .max(50, "Must be maximum 50 characters"),
      password: yup
        .string()
        .required("Password is required!")
        .min(6, "Must be at least 6 characters!")
        .max(15, "Must be maximum 15 characters!"),
    });

    return {
      successful: false,
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
  mounted() {
    if (this.loggedIn) {
      this.$router.push("/movies");
    }
  },
  methods: {
    handleRegister(user) {
      this.message = "";
      this.successful = false;
      this.loading = true;

      this.$store.dispatch("auth/register", user).then(
        (data) => {
          this.successful = true;
          this.loading = false;
          this.$router.push("/movies");
        },
        (error) => {
          if (error.response.status === 422) {
    		  this.message = error.response.data.errors;
          } else {
	          this.message =
	            (error.response &&
	              error.response.data &&
	              error.response.data.message) ||
	            error.message ||
	            error.toString();
	       	}
          this.successful = false;
          this.loading = false;
        }
      );
    },
  },
};
</script>