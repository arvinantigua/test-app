<template>
  <div class="mxa mt12" style="width: 350px">
      <Form @submit="handleSubmit" :validation-schema="schema">
        <div class="form-group">
          <label for="name">Name</label>
          <Field v-model="userName" name="name" type="text" class="form-control" />
          <ErrorMessage name="name" class="error-feedback" />
        </div>
        <div class="form-group">
          <label for="email">Email</label>
          <Field v-model="userEmail" name="email" type="email" class="form-control" />
          <ErrorMessage name="email" class="error-feedback" />
        </div>
        <w-switch
				  class="my2"
				  :model-value="false"
				  label="Update Password"
				  @input="onSwitchChange($event)">
				</w-switch>
				<div v-if="updatePassword">
					<div class="form-group">
            <label for="password">New Password</label>
            <Field name="password" type="password" class="form-control" />
            <ErrorMessage name="password" class="error-feedback" />
          </div>
          <div class="form-group">
            <label for="repeat_password">Repeat New Password</label>
            <Field name="repeat_password" type="password" class="form-control" />
            <ErrorMessage name="repeat_password" class="error-feedback" />
          </div>
				</div>

        <w-button type="submit" class="fill-width" :loading="loading" :disabled="loading" xl>
         	Update
       	</w-button>
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
        .min(6, "Must be at least 6 characters!")
        .max(15, "Must be maximum 15 characters!"),
      repeat_password: yup
        .string()
        .min(6, "Must be at least 6 characters!")
        .max(15, "Must be maximum 15 characters!")
        .oneOf([yup.ref('password'), null], "Password don't match"),
    });

    return {
      successful: false,
      loading: false,
      message: "",
      schema,
      updatePassword: false,
      userName: null,
      userEmail: null,
    };
  },
  mounted() {
    this.userName = this.$store.state.auth.user.user.name;
    this.userEmail = this.$store.state.auth.user.user.email;
  },
  methods: {
    handleSubmit(user) {
      this.message = "";
      this.successful = false;
      this.loading = true;

      this.$store.dispatch("auth/update", user).then(
        (data) => {
          this.successful = true;
          this.loading = false;
          this.$waveui.notify(`Update successful`, 'success')
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
    onSwitchChange($event) {
    	this.updatePassword = $event ? true: false;
    }
  },
};
</script>