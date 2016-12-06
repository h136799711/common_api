<div v-if="show" class="{{ alert_id }}" style="opacity: 0; display: none;">
    <div class="weui-mask_transparent"></div>
    <div class="weui-toast">
        <i class="weui-icon-success-no-circle weui-icon_toast"></i>
        <p class="weui-toast__content">{{ alert_msg }}</p>
    </div>
</div>
