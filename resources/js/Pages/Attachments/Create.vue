<template>
    <div>
        <h2>Create Attachment</h2>
        <form @submit.prevent="submit">
            <div>
                <label>Task</label>
                <select v-model="form.task_id">
                    <option
                        v-for="task in tasks"
                        :key="task.id"
                        :value="task.id"
                    >
                        {{ task.title }}
                    </option>
                </select>
            </div>
            <div>
                <label>File (PDF 100-500 kb)</label>
                <input
                    type="file"
                    @change="(e) => (form.file = e.target.files[0])"
                    accept="application/pdf"
                    required
                />
            </div>
            <div>
                <label>Is Public?</label>
                <input type="checkbox" v-model="form.is_public" />
            </div>
            <div>
                <label>Info (JSON)</label>
                <textarea v-model="form.info"></textarea>
            </div>
            <button type="submit">Create Project</button>
        </form>
    </div>
</template>

<script setup>
import { useForm } from "@inertiajs/vue3";
const props = defineProps({
    tasks: Array,
});
const form = useForm({
    task_id: "",
    file: null,
    is_public: false,
    info: "{}",
});
function submit() {
    form.post("/attachments", {
        onSuccess: () => {
            form.reset();
        },
        onError: (errors) => {
            console.error(errors);
        },
    });
}
</script>
