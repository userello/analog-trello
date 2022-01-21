<template>
    <div class="container">
        <h1>{{name}}</h1>
        <div class="form-group">
            <input type="text" @blur="saveName" v-model="name" class="form-control" :class="{ 'is-invalid': $v.name.$error }">
            <div class="invalid-feedback" v-if="!$v.name.required">
                Обязательное поле.
            </div>
            <div class="invalid-feedback" v-if="!$v.name.maxLength">
                Максимальное количество символов: {{ $v.name.$params.maxLength.max }} .
            </div>
        </div>
        <form @submit.prevent="addNewDeskList" class="mt-3">
            <div class="mb-3">
                <input type="text" v-model="desk_list_name" class="form-control" :class="{ 'is-invalid': $v.desk_list_name.$error }" placeholder="Введите название списка">
                <div class="invalid-feedback" v-if="!$v.desk_list_name.required">
                    Обязательное поле.
                </div>
                <div class="invalid-feedback" v-if="!$v.desk_list_name.maxLength">
                    Максимальное количество символов: {{ $v.desk_list_name.$params.maxLength.max }} .
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Добавить список</button>
        </form>
        <div class="alert alert-danger" role="alert" v-if="errored">
            Ошибка загрузки данных.
        </div>
        <div class="row">
            <div class="col-lg-4" v-for="(desk_list, index) in desk_lists">
                <div class="card mt-3">
                    <div class="card-body">
                        <form @submit.prevent="updateDeskList(desk_list.id, desk_list.name)" v-if="desk_list_input_id == desk_list.id" class="d-flex justify-content-between align-items-center">
                            <input type="text" v-model="desk_list.name" class="form-control" :class="{ 'is-invalid': $v.desk_lists.$each[index].name.$error }" placeholder="Введите название списка">
                            <div class="invalid-feedback" v-if="!$v.desk_lists.$each[index].name.required">
                                Обязательное поле.
                            </div>
                            <div class="invalid-feedback" v-if="!$v.desk_lists.$each[index].name.maxLength">
                                Максимальное количество символов: {{ $v.desk_lists.$each[index].name.$params.maxLength.max }} .
                            </div>
                            <button type="button" @click="updateDeskList(desk_list.id, desk_list.name)" class="btn-close" aria-label="Close"></button>
                        </form>
                        <h4 v-else class="card-title d-flex justify-content-between align-items-center" @click="desk_list_input_id = desk_list.id">{{ desk_list.name }}<i class="fas fa-pencil-alt" style="font-size: 15px; cursor:pointer;"></i></h4>
                        <button type="button" class="btn btn-danger mt-3" @click="deleteDeskList(desk_list.id)">Удалить список</button>
                        <div class="card mt-3 bg-light" v-for="card in desk_list.cards" :key="card.id">
                            <div class="card-body">
                                <h4 class="card-title d-flex justify-content-between align-items-center mb-3">{{ card.name }}</h4>
                                <button type="button" @click="getCard(card.id)" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal">
                                    Открыть
                                </button>
                                <button type="button" class="btn btn-secondary" @click="deleteCard(card.id)">Удалить</button>
                            </div>
                        </div>
                        <form @submit.prevent="addNewCard(desk_list.id)" class="mt-3">
                            <input type="text" v-model="card_names[desk_list.id]" class="form-control" :class="{ 'is-invalid': $v.card_names.$each[desk_list.id].$error }" placeholder="Введите название карточки">
                            <div class="invalid-feedback" v-if="!$v.card_names.$each[desk_list.id].required">
                                Обязательное поле.
                            </div>
                            <div class="invalid-feedback" v-if="!$v.card_names.$each[desk_list.id].maxLength">
                                Максимальное количество символов: {{ $v.card_names.$each[desk_list.id].$params.maxLength.max }} .
                            </div>
                        </form>

                        <!-- Modal -->
                        <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <form @submit.prevent="updateCardName()" v-if="show_card_name_input" class="d-flex justify-content-between align-items-center">
                                            <input type="text" v-model="current_card.name" class="form-control" :class="{ 'is-invalid': $v.current_card.name.$error }" placeholder="Введите название карточки">
                                            <div class="invalid-feedback" v-if="!$v.current_card.name.required">
                                                Обязательное поле.
                                            </div>
                                            <div class="invalid-feedback" v-if="!$v.current_card.name.maxLength">
                                                Максимальное количество символов: {{ $v.current_card.name.$params.maxLength.max }} .
                                            </div>
                                            <button type="button" class="btn-close" aria-label="Close" @click="updateCardName"></button>
                                        </form>
                                        <h5 class="modal-title" id="exampleModalLabel" v-if="!show_card_name_input" @click="show_card_name_input = true">{{ current_card.name }} <i class="fas fa-pencil-alt" style="font-size: 15px; cursor:pointer;"></i></h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="form-check" v-for="(task, index) in current_card.tasks">
                                            <input type="checkbox" @change="updateTask(task)" class="form-check-input" :id="'inlineCheckbox' + index" v-model="task.is_done">
                                            <form @submit.prevent="updateTask(task)" v-if="task_input_name_id == task.id" style="display: inline;">
                                                <input type="text" v-model="task.name" class="form-control" style="display: inline; width: 50%;" placeholder="Введите название задачи">
                                            </form>
                                            <label v-else class="form-check-label" :for="'inlineCheckbox' + index">{{ task.name }}</label>
                                            <span @click="task_input_name_id = task.id" v-if="task_input_name_id != task.id"><i class="fas fa-pencil-alt" style="font-size: 15px; cursor:pointer;"></i></span>
                                            <button type="button" @click="deleteTask(task.id)" class="btn-close" aria-label="Close" style="float: right;"></button>
                                        </div>
                                    </div>
                                    <form @submit.prevent="addNewTask" class="p-3">
                                        <div class="mb-3">
                                            <input type="text" v-model="new_task_name" class="form-control" :class="{ 'is-invalid': $v.new_task_name.$error }" placeholder="Введите название задачи">
                                            <div class="invalid-feedback" v-if="!$v.new_task_name.required">
                                                Обязательное поле.
                                            </div>
                                            <div class="invalid-feedback" v-if="!$v.new_task_name.maxLength">
                                                Максимальное количество символов: {{ $v.new_task_name.$params.maxLength.max }} .
                                            </div>
                                        </div>
                                    </form>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="spinner-border text-primary" role="status" v-if="loading">
            <span class="visually-hidden">Loading...</span>
        </div>
    </div>
</template>

<script>
import { required, maxLength} from 'vuelidate/lib/validators'
export default {
    props: [
        'deskId'
    ],
    data() {
        return {
            name: null,
            desk_list_name: null,
            errored: false,
            loading: true,
            desk_lists: null,
            desk_list_input_id: null,
            card_names: [],
            current_card: [],
            show_card_name_input: false,
            new_task_name: '',
            task_input_name_id: null
        }
    },
    methods: {
        updateTask(task) {
            axios.post('/api/v1/tasks/' + task.id, {
                _method: 'PATCH',
                name: task.name,
                is_done: task.is_done,
                card_id: task.card_id,
            })
                .then(response => {
                    console.log(response.data);
                    this.task_input_name_id = null
                })
                .catch(error => {
                    //console.log(error.response)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        deleteTask(id) {
            if(confirm('Вы действительно хотите удалить эту задачу?')) {
                axios.post('/api/v1/tasks/' + id, {
                    _method: 'DELETE'
                })
                    .then(response => {
                        this.getCard(this.current_card.id)
                    })
                    .catch(error => {
                        console.log(error.response)
                        this.errored = true
                    })
                    .finally(() => {
                        this.loading = false
                    })
            }
        },
        addNewTask() {
            this.$v.new_task_name.$touch()
            if(this.$v.new_task_name.$anyError) {
                return;
            }
            axios.post('/api/v1/tasks', {
                name: this.new_task_name,
                card_id: this.current_card.id,
            })
                .then(response => {
                    this.new_task_name = ''
                    this.$v.$reset()
                    this.getCard(this.current_card.id)
                })
                .catch(error => {
                    console.log(error.response)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        updateCardName() {
            this.$v.current_card.name.$touch()
            if(this.$v.current_card.name.$anyError) {
                return;
            }
            this.show_card_name_input = false
            axios.post('/api/v1/cards/' + this.current_card.id, {
                _method: 'PATCH',
                name: this.current_card.name,
            })
                .then(response => {
                    this.show_card_name_input = false
                    this.$v.$reset()
                    this.getDeskLists()
                })
                .catch(error => {
                    console.log(error.response)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        getCard(id) {
            axios.get('/api/v1/cards/' + id)
                .then(response => {
                    this.current_card = response.data.data
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        deleteCard(id) {
            if(confirm('Вы действительно хотите удалить эту карточку?')) {
                axios.post('/api/v1/cards/' + id, {
                    _method: 'DELETE'
                })
                    .then(response => {
                        this.getDeskLists()
                    })
                    .catch(error => {
                        console.log(error.response)
                        this.errored = true
                    })
                    .finally(() => {
                        this.loading = false
                    })
            }
        },
        addNewCard(desk_list_id) {
            this.$v.card_names.$each[desk_list_id].$touch()
            if(this.$v.card_names.$each[desk_list_id].$anyError) {
                return;
            }
            axios.post('/api/v1/cards', {
                name: this.card_names[desk_list_id],
                desk_list_id
            })
                .then(response => {
                    this.$v.$reset()
                    this.card_names[desk_list_id] = ''
                    this.getDeskLists()
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
        },
        updateDeskList(id, name) {
            this.$v.desk_lists.$touch()
            if(this.$v.desk_lists.$anyError) {
                return;
            }
            this.desk_list_input_id = null
            axios.post('/api/v1/desk-lists/' + id, {
                name,
                _method: 'PUT'
            })
                .then(response => {
                    this.desk_list_input_id = null
                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        deleteDeskList(id) {
            if(confirm('Вы действительно хотите удалить этот список?')) {
                axios.post('/api/v1/desk-lists/' + id, {
                    _method: 'DELETE'
                })
                    .then(response => {
                        this.desk_lists = []
                        this.getDeskLists()
                    })
                    .catch(error => {
                        console.log(error)
                        this.errored = true
                    })
                    .finally(() => {
                        this.loading = false
                    })
            }
        },
        getDeskLists() {
            axios.get('/api/v1/desk-lists', {
                params: {
                    desk_id: this.deskId
                }
            })
                .then(response => {
                    this.desk_lists = response.data.data
                    this.desk_lists.forEach(el => {
                        this.card_names[el.id] = ''
                    })
                })
                .catch(error => {
                    console.log(error.response)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        saveName() {
            this.$v.name.$touch()
            if(this.$v.name.$anyError) {
                return;
            }
            axios.post('/api/v1/desks/' + this.deskId, {
                _method: 'PUT',
                name: this.name,
            })
                .then(response => {

                })
                .catch(error => {
                    console.log(error)
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        },
        addNewDeskList() {
            this.$v.desk_list_name.$touch()
            if(this.$v.desk_list_name.$anyError) {
                return;
            }
            axios.post('/api/v1/desk-lists', {
                name: this.desk_list_name,
                desk_id: this.deskId,
            })
                .then(response => {
                    this.$v.$reset()
                    this.desk_list_name = ''
                    this.desk_lists = []
                    this.getDeskLists()
                })
                .catch(error => {
                    console.log(error.response)
                    // if(error.response.data.errors.name) {
                    //     this.errors = []
                    //     this.errors.push(error.response.data.errors.name[0])
                    // }
                    this.errored = true
                })
                .finally(() => {
                    this.loading = false
                })
        }
    },
    mounted() {
        axios.get('/api/v1/desks/' + this.deskId)
            .then(response => {
                this.name = response.data.data.name
            })
            .catch(error => {
                console.log(error)
                this.errored = true
            })
            .finally(() => {
                this.loading = false
            })
        this.getDeskLists()
    },
    validations: {
        name: {
            required,
            maxLength: maxLength(55)
        },
        desk_list_name: {
            required,
            maxLength: maxLength(55)
        },
        card_names: {
            $each: {
                required,
                maxLength: maxLength(55)
            }
        },
        current_card: {
            name: {
                required,
                maxLength: maxLength(5)
            }
        },
        desk_lists: {
            $each: {
                name: {
                    required,
                    maxLength: maxLength(55)
                }
            }
        },
        new_task_name: {
            required,
            maxLength: maxLength(55)
        }
    }
}
</script>
