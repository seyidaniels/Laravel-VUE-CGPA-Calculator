@extends('layouts.app')

@section('content')
<div class="container">
    <div id="cgpa" class="row justify-content-center">
        <div class="col-md-7">
            <div class="card mt-4">

               
                <div class="card-header">YOUR CGPA:  @{{cgpa}}</div>



                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif
                    <button @click="toggleCalc()" class="btn btn-primary">
                        <span v-if="!showCalculator">Calculate GPA</span>
                        <span v-if="showCalculator"> Hide Calculator </span>
                    </button>

                    <div class="row">
                            <table class="table">
                                    <p> Your Recent GPAs </p>

                                    <thead>
                                      <tr>
                                        <th scope="col">#</th>
                                        <th scope="col">GPA </th>
                                        <th scope="col">Date Created</th>
                                      </tr>
                                    </thead>
                                    <tbody>
                                        @foreach (Auth::user()->gpas as $key => $gpa)
                                            
                                      <tr > 
                                        <th scope="row">{{$key+1}}</th>
                                        <td> {{$gpa->score()}} </td>
                                            @php
                                                $date = \Carbon\Carbon::parse($gpa->created_at);
                                            @endphp
                                        <td> {{$date->diffforhumans()}} </td>
                                      </tr>

                                      @endforeach

                                      
                                    </tbody>
                                  </table>

                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-5" v-if="showCalculator">
                <div class="card mt-4">
                    <div class="card-header"> This is where Calculation Goes</div>
    
                    <div class="card-body">

                        <div class="row my-4" v-for="(score, index) in scores" :key="index" >
                            <div class="col-3">
                                    <select v-model="score.unit" class="form-control" name="grade" id="">
                                            <option selected disabled>Unit</option>
                                            <option value="1">1</option>
                                            <option value="2">2</option>
                                            <option value="3">3</option>
                                            <option value="4">4</option>
                                            <option value="5">5</option>
                                            <option value="6">6</option>
                                    </select>
                            </div>
                            <div class="col-3">
                                    <select v-model="score.grade" class="form-control" name="grade" id="">
                                        <option selected disabled>Grade</option>
                                        <option value="5">A</option>
                                        <option value="4">B</option>
                                        <option value="3">C</option>
                                        <option value="2">D</option>
                                        <option value="1">E</option>
                                        <option value="0">F</option>
                                    </select>
                            </div>
                            <div class="col-4">
                                <input type="text" disabled v-model="getName(index)"  placeholder="Course Name" class="form-control">
                            </div>
                            <div class="col-2">
                                <button @click="deleteScore(index)" class="btn-sm btn-danger">delete</button>
                            </div>
                        </div>

                        <div class="row mt-4">
                                <button @click="calculate" class="btn btn-primary float-right">
                                        Calculate GPA
                                </button>

                                <button @click="addScore" class="btn btn-warning ml-4 btn-sm float-left">
                                        Add
                                </button>

                                <button @click="saveToDB" v-if="gpa" class="btn btn-success ml-4 btn-sm float-left">
                                       Save to DB
                                </button>
                        </div>
                       
                       
                    </div>
                </div>
            </div>
    </div>
</div>
@endsection
@section('extra-js')
<script src="https://cdn.jsdelivr.net/npm/vue@2.6.10/dist/vue.js"></script>
<script src="http://code.jquery.com/jquery-1.9.1.min.js"></script>
<link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/css/toastr.css" rel="stylesheet"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/2.0.1/js/toastr.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/axios/0.19.0/axios.min.js" integrity="sha256-S1J4GVHHDMiirir9qsXWc8ZWw74PHHafpsHp5PXtjTs=" crossorigin="anonymous"></script>
<script>
    new Vue ({
        el: "#cgpa",
        mounted (){
            this.scores.push({
                        grade: "",
                        unit: "",
                        name: ""   
                    })
        },
        data: function () {
            return {
                showCalculator: false,
                scores: [],
                totalUnits: 0,
                totalScore: 0,
                gpa: 0.00,
                cgpa: parseFloat({!! json_encode(Auth::user()->cpga()) !!}).toFixed(2)
            }
        },
        methods: {
            toggleCalc() {
                this.showCalculator = !this.showCalculator;
            },
            deleteScore(index) {   
                if (this.scores.length == 1){
                    toastr.error("You cant remove last score");
                    return;
                } 
                this.scores.splice(index, 1);
            },
            addScore() {
                this.scores.push({
                        grade: "",
                        unit: "",
                        name: ""   
                    })
            },
            calculate(){
                let i = 0;
                this.totalScore = 0
                this.totalUnits = 0
                this.scores.forEach(score => {
                    i++;
                    if (score.unit == "" || score.grade == "" ) {
                        toastr.error ("Course "+i +" does not have unit/grade")
                        return;
                    }
                    this.totalUnits += parseInt(score.unit)
                    this.totalScore += parseInt(score.unit) * parseInt(score.grade)
                    
                })

                this.gpa = parseFloat(this.totalScore / this.totalUnits).toFixed(2);

                toastr.success ("Your GPA is "+this.gpa)


            },
            getName(index) {
                number = parseInt(index) + 1
                return "COURSE "+ number
            },
            saveToDB () {
                axios.post ("save-gpa", 
                    {
                        total_score: this.totalScore,
                        total_units: this.totalUnits
                    }
                    ).then (response => {
                        if (response.data.message) {
                            this.cgpa = response.data.cgpa
                            toastr.success(response.data.message)
                            this.gpa = 0.00;
                            this.mounted()
                        }
                    })
            }
        }
    })
</script>
@endsection
