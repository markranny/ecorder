<script setup>
import { ref, computed } from 'vue';
import { useForm } from '@inertiajs/vue3';
import Modal from "@/Components/Modals/DaisyModal.vue";
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue';
import InputLabel from '@/Components/Inputs/InputLabel.vue';
import TextInput from '@/Components/Inputs/TextInput.vue';
import SelectOption from '@/Components/SelectOption/SelectOption.vue';
import InputError from '@/Components/Inputs/InputError.vue';
import FormComponent from '@/Components/Form/FormComponent.vue';


const props = defineProps({
    showModal: {
        type: Boolean,
        default: false,
    }
});

const initialStoreId = computed(() => {
  return $page.props.auth.user.storeid;
});

const form = useForm({
    storeid: '',
    description: '',
});

const submitForm = () => {
    form.post("/order", {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const emit = defineEmits();

const toggleActive = () => {
    emit('toggleActive');
};

</script>

<template>
    <Modal title="CREATE NEW ORDER" @toggle-active="toggleActive" :show-modal="showModal">
        <template #content >
            <FormComponent @submit.prevent="submitForm"  >
                
                <div class="grid grid-cols-1">
                    <div class="col-span-1">
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <InputLabel for="STORE" value="Your order has been automatically generated with transfer request #" />
                                <TextInput
                                    id="storeid"
                                    v-model="form.storeid"
                                    type="text"
                                    class="mt-1 block w-full hidden"
                                    :is-error="form.errors.storeid ? true : false"
                                    :value="$page.props.auth.user.storeid"
                                    autofocus
                                    disabled
                                />
                                <InputError :message="form.errors.storeid" class="mt-2" />
                            </div>
                        </div>
                    </div>
                </div>
                
            </FormComponent>
        </template>
        <template #buttons>
            <PrimaryButton type="submit" @click="submitForm" :disabled="form.processing" :class="{ 'opacity-25': form.processing }">
                Generate
            </PrimaryButton>
        </template>
    </Modal>
</template>

