<script setup>
import { useForm } from '@inertiajs/vue3';
import { onMounted, watch } from 'vue';
import Modal from "@/Components/Modals/Modal.vue";
import PrimaryButton from '@/Components/Buttons/PrimaryButton.vue';
import InputLabel from '@/Components/Inputs/InputLabel.vue';
import TextInput from '@/Components/Inputs/TextInput.vue';
import InputError from '@/Components/Inputs/InputError.vue';

const props = defineProps({
    showModal: {
        type: Boolean,
        default: false,
    },
    DISCVALIDPERIODID: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    OFFERID: '',
    DESCRIPTION: '',
    STATUS: '',
    PDTYPE: '',
    PRIORITY: '',
    DISCVALIDPERIODID: '',
    DISCOUNTTYPE: '',
    NOOFLINESTOTRIGGER: '',
    DEALPRICEVALUE: '',
    DISCOUNTPCTVALUE: '',
    DISCOUNTAMOUNTVALUE: '',
    PRICEGROUP: '',
});

const submitForm = () => {
    form.post("/posperiodicdiscounts", {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
};

const emit = defineEmits();

const toggleActive = () => {
    emit('toggleActive');
};

onMounted(() => {
    form.DISCVALIDPERIODID = props.DISCVALIDPERIODID;
});

watch(
    () => props.DISCVALIDPERIODID,
    (newValue) => {
        form.DISCVALIDPERIODID = newValue;
    }
);

</script>

<template>
    <Modal title="MIX AND MATCH ENTRIES" @toggle-active="toggleActive" :show-modal="showModal">
        <template #content >
            <form @submit.prevent="submitForm"   class="px-2 py-3 max-h-[50vh] lg:max-h-[70vh] overflow-y-auto">
                <div class="grid grid-cols-4 ">

                    <div class="col-span-6 hidden">
                    <InputLabel for="OFFERID" value="OFFERID" />
                    <TextInput
                        id="OFFERID"
                        v-model="form.OFFERID"
                        type="text"
                        class="mt-1 block w-full"
                        :is-error="form.errors.OFFERID ? true : false"
                        autofocus
                    />
                    <InputError :message="form.errors.OFFERID" class="mt-2" />
                    </div>  

                    <div class="col-span-6 mt-4">
                    <InputLabel for="DESCRIPTION" value="DESCRIPTION" />
                    <TextInput
                        id="DESCRIPTION"
                        v-model="form.DESCRIPTION"
                        :is-error="form.errors.DESCRIPTION ? true : false"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.DESCRIPTION" class="mt-2" />
                    </div>

                    <div class="col-span-2 mt-4">
                        <InputLabel for="STATUS" value="STATUS" />
                            <select 
                            className="select select-bordered w-full max-w-max-w-full"
                            id="STATUS"
                            name="STATUS"
                            v-model="form.STATUS"
                            :is-error="form.errors.STATUS ? true : false"
                            autofocus
                            >
                            <option disabled selected>STATUS</option>
                            <option value="1">Enabled</option>
                            <option value="0">Disabled</option>
                        </select>
                        <InputError :message="form.errors.STATUS" class="mt-2" />
                        
                    </div>

                    <div class="col-span-2 mt-4 ml-4">
                        <InputLabel for="DISCOUNTTYPE" value="DISCOUNTTYPE" />
                        <select 
                            className="select select-bordered w-full max-w-full"
                            id="DISCOUNTTYPE"
                            name="DISCOUNTTYPE"
                            v-model="form.DISCOUNTTYPE"
                            :is-error="form.errors.DISCOUNTTYPE ? true : false"
                            autofocus
                            >
                            <option disabled selected>DISCOUNTTYPE</option>
                            <option value="1">Deal Price</option>
                            <option value="2">Discount Percent</option>
                            <option value="3">Discount Amount</option>
                            <option value="4">Line Specific</option>
                        </select>
                        <InputError :message="form.errors.DISCOUNTTYPE" class="mt-2" />

                    </div>

                    <div class="col-span-2 mt-4 ml-4">
                        <InputLabel for="DISCVALIDPERIOD" value="DISCVALIDPERIOD" />
                        <select
                            class="select select-bordered w-full max-w-xs"
                            id="DISCVALIDPERIODID"
                            name="DISCVALIDPERIODID"
                            v-model="form.DISCVALIDPERIODID"
                            :class="{ 'input-error': form.errors.DISCVALIDPERIODID }"
                            autofocus
                        >
                            <option disabled selected>DISCVALIDPERIOD</option>
                            <option selected>None</option>
                            <option v-for="period in DISCVALIDPERIODID" :key="period.ID" :value="period.ID">
                                {{ period.DESCRIPTION }}
                            </option>
                        </select>
                        <InputError :message="form.errors.DISCVALIDPERIODID" class="mt-2" />
                    </div>

                    <!-- <div class="col-span-1 mt-4 ml-4">
                    <InputLabel for="PDTYPE" value="PDTYPE" />
                    <TextInput
                        id="PDTYPE"
                        v-model="form.PDTYPE"
                        :is-error="form.errors.PDTYPE ? true : false"
                        type="number"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.PDTYPE" class="mt-2" />
                    </div> -->

                    <!-- <div class="col-span-1">
                        <InputLabel for="PRIORITY" value="PRIORITY" />
                    <TextInput
                        id="PRIORITY"
                        v-model="form.PRIORITY"
                        :is-error="form.errors.PRIORITY ? true : false"
                        type="number"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.PRIORITY" class="mt-2" />
                    </div> -->

                    
                    
                    <!-- <div class="col-span-1 ml-4">
                        <InputLabel for="NOOFLINESTOTRIGGER" value="NOOFLINESTOTRIGGER" />
                    <TextInput
                        id="NOOFLINESTOTRIGGER"
                        v-model="form.NOOFLINESTOTRIGGER"
                        :is-error="form.errors.NOOFLINESTOTRIGGER ? true : false"
                        type="number"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.NOOFLINESTOTRIGGER" class="mt-2" />
                    </div> -->

                    <div class="col-span-2 mt-4">
                        <InputLabel for="DEALPRICEVALUE" value="DEALPRICEVALUE" />
                        <TextInput
                            id="DEALPRICEVALUE"
                            v-model="form.DEALPRICEVALUE"
                            :is-error="form.errors.DEALPRICEVALUE ? true : false"
                            type="number"
                            class="w-full"
                        />
                        <InputError :message="form.errors.DEALPRICEVALUE" class="mt-2" />
                    </div>

                    <div class="col-span-2 ml-4 mt-4">
                        <InputLabel for="DISCOUNTPCTVALUE" value="DISCOUNTPCTVALUE" />
                    <TextInput
                        id="DISCOUNTPCTVALUE"
                        v-model="form.DISCOUNTPCTVALUE"
                        :is-error="form.errors.DISCOUNTPCTVALUE ? true : false"
                        type="number"
                    />
                    <InputError :message="form.errors.DISCOUNTPCTVALUE" class="mt-2" />
                    </div>

                    <div class="col-span-2 ml-4 mt-4">
                        <InputLabel for="DISCOUNTAMOUNTVALUE" value="DISCOUNTAMOUNTVALUE" />
                    <TextInput
                        id="DISCOUNTAMOUNTVALUE"
                        v-model="form.DISCOUNTAMOUNTVALUE"
                        :is-error="form.errors.DISCOUNTAMOUNTVALUE ? true : false"
                        type="number"
                    />
                    <InputError :message="form.errors.DISCOUNTAMOUNTVALUE" class="mt-2" />
                    </div>

                    

                    <!-- <div class="col-span-3">
                        <InputLabel for="PRICEGROUP" value="PRICEGROUP" />
                    <TextInput
                        id="PRICEGROUP"
                        v-model="form.PRICEGROUP"
                        :is-error="form.errors.PRICEGROUP ? true : false"
                        type="text"
                        class="mt-1 block w-full"
                    />
                    <InputError :message="form.errors.PRICEGROUP" class="mt-2" />
                    </div> -->
                </div>
            </form>
        </template>
        <template #buttons>
            <PrimaryButton type="submit" @click="submitForm" :disabled="form.processing" :class="{ 'opacity-25': form.processing }">
                SUBMIT
            </PrimaryButton>
        </template>
    </Modal>
</template>
