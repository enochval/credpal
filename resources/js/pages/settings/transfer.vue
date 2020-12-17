<template>
  <card :title="$t('transfer_fund')">
    <form @submit.prevent="transferFund" @keydown="form.onKeydown($event)">
      <alert-success :form="form" :message="$t('transfer_successful')" />

      <!-- Password -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('transfer_type') }}</label>
        <div class="col-md-7">
          <select v-model="form.transfer_type" class="form-control" :class="{ 'is-invalid': form.errors.has('transfer_type') }">
            <option>Instant</option>
            <option>Future</option>
          </select>
          <has-error :form="form" field="transfer_type" />
        </div>
      </div>
      <!-- Password -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('receiver_account_no') }}</label>
        <div class="col-md-7">
          <input v-model="form.account_no" :class="{ 'is-invalid': form.errors.has('account_no') }" class="form-control" type="number" name="account_no">
          <has-error :form="form" field="account_no" />
        </div>
      </div>

      <!-- Password Confirmation -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('amount') }}</label>
        <div class="col-md-7">
          <input v-model="form.amount" :class="{ 'is-invalid': form.errors.has('amount') }" class="form-control" type="number" name="amount">
          <has-error :form="form" field="amount" />
        </div>
      </div>

      <!-- Password Confirmation -->
      <div v-if="form.transfer_type.toLowerCase() === 'future'" class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('future_transfer_date') }}</label>
        <div class="col-md-7">
          <datepicker v-model="form.future_date" :class="{ 'is-invalid': form.errors.has('future_date') }" class="form-control" placeholder="Select Date"></datepicker>
          <has-error :form="form" field="future_date" />
        </div>
      </div>

      <!-- Submit Button -->
      <div class="form-group row">
        <div class="col-md-9 ml-md-auto">
          <v-button :loading="form.busy" type="success">
            {{ $t('transfer_fund') }}
          </v-button>
        </div>
      </div>
    </form>
  </card>
</template>

<script>
import Form from 'vform'
import Datepicker from 'vuejs-datepicker'

export default {
  components: {
    Datepicker
  },
  scrollToTop: false,

  metaInfo () {
    return { title: this.$t('settings') }
  },

  data: () => ({
    form: new Form({
      transfer_type: 'Instant',
      future_date: '',
      amount: '',
      account_no: ''
    })
  }),

  methods: {
    async transferFund () {
      await this.form.post('/api/settings/transfer-fund')

      this.form.reset()

      // Fetch the user.
      await this.$store.dispatch('auth/fetchUser')

      this.$router.push({ name: 'settings.wallet' })
    }
  }
}
</script>
