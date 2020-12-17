<template>
  <card :title="$t('your_info')">
    <form @submit.prevent="update" @keydown="form.onKeydown($event)">
      <alert-success :form="form" :message="$t('info_updated')" />

      <!-- Name -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('name') }}</label>
        <div class="col-md-7">
          <input :value="user.name" class="form-control" type="text" disabled>
        </div>
      </div>

      <!-- Email -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('email') }}</label>
        <div class="col-md-7">
          <input :value="user.email" class="form-control" type="email" disabled>
        </div>
      </div>

      <!-- Phone -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('phone') }}</label>
        <div class="col-md-7">
          <input :value="user.phone" class="form-control" type="text" disabled>
        </div>
      </div>

      <!-- Referral link -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('referral_link') }}</label>
        <div class="col-md-7">
          <input class="form-control" type="text" :value="referralLink" disabled>
        </div>
      </div>

      <!-- Name -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('valid_id') }}</label>
        <div class="col-md-7">
          <img :src="user.valid_id ? user.valid_id : form.valid_id" style="max-width: 90%">
          <input type="file" name="image" class="form-control"
                 accept="image" @change="onImageChange">
          <has-error :form="form" field="valid_id" />
        </div>
      </div>

      <!-- Submit Button -->
      <div class="form-group row">
        <div class="col-md-9 ml-md-auto">
          <v-button :loading="form.busy" type="success">
            {{ $t('update_valid_id') }}
          </v-button>
        </div>
      </div>
    </form>
  </card>
</template>

<script>
import Form from 'vform'
import { mapGetters } from 'vuex'

export default {
  scrollToTop: false,

  metaInfo () {
    return { title: this.$t('settings') }
  },

  data: () => ({
    form: new Form({
      valid_id: ''
    }),
    referralLink: ''
  }),

  computed: mapGetters({
    user: 'auth/user'
  }),

  created () {
    // Fill the form with user data.
    this.form.keys().forEach(key => {
      this.form[key] = this.user[key]
    })
    this.referralLink = window.location.origin + '/create-account?ref=' + this.user.referral_code
  },

  methods: {
    async update () {
      const { data } = await this.form.patch('/api/settings/profile')

      this.$store.dispatch('auth/updateUser', { user: data })
    },
    async onImageChange (e) {
      e.preventDefault()

      if (e.target.files[0]) {
        const FR = new FileReader()
        const vm = this

        FR.addEventListener('load', function (e) {
          vm.form.valid_id = e.target.result
        })

        FR.readAsDataURL(e.target.files[0])
      }
    }
  }
}
</script>
