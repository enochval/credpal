<template>
  <card :title="$t('wallet')">
    <form>
      <!-- Name -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('account_no') }}</label>
        <div class="col-md-7">
          <input :value="user.account_no" class="form-control" type="text" disabled>
        </div>
      </div>

      <!-- Email -->
      <div class="form-group row">
        <label class="col-md-3 col-form-label text-md-right">{{ $t('wallet_balance') }} (&#8358;)</label>
        <div class="col-md-7">
          <input :value="user.balance" class="form-control" type="text" disabled>
        </div>
      </div>
    </form>
  </card>
</template>

<script>
import { mapGetters } from 'vuex'

export default {
  scrollToTop: false,

  metaInfo () {
    return { title: this.$t('settings') }
  },

  data: () => ({
  }),

  computed: mapGetters({
    user: 'auth/user'
  })
}
</script>
