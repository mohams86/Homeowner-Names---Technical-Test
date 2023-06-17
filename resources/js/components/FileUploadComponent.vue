<template>
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card">
          <div class="card-header">Laravel Vue JS File Upload</div>
          <div class="card-body">
            <div v-if="success_msg != ''" class="alert alert-success">
              {{ success_msg }}
            </div>
            <div v-if="error_msg != ''" class="alert alert-danger">
              {{ error_msg }}
            </div>
            <form @submit="formSubmit" enctype="multipart/form-data">
              <input
                type="file"
                accept=".csv"
                class="form-control mb-3"
                v-on:change="onChange"
              />
              <button class="btn btn-primary mb-3">Upload</button>
            </form>
            <div v-if="finalperson_arr != ''">
              <table class="table table-bordered">
                <thead>
                  <tr>
                    <th class="text-center">Title</th>
                    <th class="text-center">First Name</th>
                    <th class="text-center">Initial</th>
                    <th class="text-center">Last Name</th>
                  </tr>
                </thead>
                <tr v-for="persondata in finalperson_arr">
                  <td class="text-center">{{ persondata.title }}</td>
                  <td class="text-center">{{ persondata.first_name }}</td>
                  <td class="text-center">{{ persondata.initial }}</td>
                  <td class="text-center">{{ persondata.last_name }}</td>
                </tr>
              </table>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";
export default {
  data() {
    return {
      name: "",
      file: "",
      success_msg: "",
      finalperson_arr: "",
      error_msg: "",
    };
  },
  methods: {
    onChange(e) {
      this.file = e.target.files[0];
    },
    formSubmit(e) {
      let existingObj = this;
      if (!this.file) {
        e.preventDefault();
        existingObj.error_msg = "Please select a file to upload";
        return;
      }
      if (this.file.type != "text/csv") {
        e.preventDefault();
        existingObj.error_msg = "Please select a valid csv file";
        return;
      }
      e.preventDefault();
      const config = {
        headers: {
          "content-type": "multipart/form-data",
        },
      };
      let data = new FormData();
      data.append("file", this.file);
      axios
        .post("/upload", data, config)
        .then(function (res) {
          existingObj.success_msg = res.data.success;
          existingObj.finalperson_arr = res.data.finalperson_arr;
          existingObj.error_msg = "";
        })
        .catch(function (err) {
          existingObj.error_msg = err;
        });
    },
  },
};
</script>
