@extends('layouts.main')
@section('title', 'Ujian Online')

@section('content')
    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Latihan Soal - {{ $questions->judul }}</h4>
                        </div>
                        @include('partials.alert')
                        <div class="card-body">
                            <form id="quizForm" action="{{ route('update-nilai-latihan', $questions->id) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-3">
                                        <div class="list-group" id="question-list">
                                            @foreach ($questions->soals as $key => $question)
                                                <button type="button"
                                                    class="list-group-item list-group-item-action question-btn"
                                                    data-id="{{ $question->id }}"
                                                    style="background-color: {{ $question->answered ? '#007bff' : '#d6d6d6' }}; color: {{ $question->answered ? '#fff' : '#000' }};">
                                                    Soal {{ $key + 1 }}
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-9">
                                        <div id="question-container">
                                            {{-- @dd() --}}
                                            @foreach ($questions->soals as $key => $question)
                                            @php
                                                    $cekjawaban = App\Models\JawabanLatihan::where(
                                                        'id_siswa',
                                                        $id_siswa,
                                                    )
                                                        ->where('id_soal', $question->id)
                                                        ->where('id_latihan_soal', $questions->id)
                                                        ->first();
                                                @endphp
                                            {{-- @dd($id_siswa, $question->id, $questions->id,$cekjawaban); --}}

                                                <div class="question" id="question-{{ $question->id }}"
                                                    style="display: none;">
                                                    <h5>{{ $question->pertanyaan }}</h5>
                                                    @if ($question->tipe_soal === 'pilihan_ganda')
                                                     
                                                        <div class="form-group">
                                                            @php
                                                                $pil = 'A';
                                                            @endphp
                                                            @foreach ($question->pilihan as $optionKey => $option)
                                                                <div class="option-container">
                                                                    <input type="checkbox" class="option-checkbox"
                                                                        name="answers[{{ $question->id }}][]"
                                                                        value="Opsi {{ $pil }}"
                                                                        id="option-{{ $question->id }}-{{ $optionKey }}"
                                                                        {{ isset($cekjawaban->jawaban) && strpos($cekjawaban->jawaban, 'Opsi '.$pil) !== false ? 'checked' : '' }}>

                                                                    <label
                                                                        for="option-{{ $question->id }}-{{ $optionKey }}">
                                                                        <span class="option-btn">
                                                                            {{ $pil++ }}. {{ $option }}
                                                                        </span>
                                                                    </label>
                                                                </div>

                                                            @endforeach
                                                        </div>

                                                        <style>
                                                            .option-container {
                                                                display: inline-block;
                                                                margin: 5px;
                                                            }

                                                            .option-checkbox {
                                                                display: none;
                                                            }

                                                            .option-btn {
                                                                display: inline-block;
                                                                padding: 10px 20px;
                                                                background-color: #f8f9fa;
                                                                border: 2px solid #007bff;
                                                                border-radius: 5px;
                                                                cursor: pointer;
                                                                transition: background-color 0.3s, transform 0.3s;
                                                            }

                                                            .option-checkbox:checked+label .option-btn {
                                                                background-color: #007bff;
                                                                color: black;
                                                                transform: scale(1.1);
                                                            }

                                                            .option-checkbox:checked+label .option-btn:active {
                                                                transform: scale(1);
                                                                color: black;
                                                            }

                                                            .option-btn:hover {
                                                                background-color: silver;
                                                            }

                                                            .option-checkbox:checked+label .option-btn:hover {
                                                                color: black;
                                                                background-color: #0056b3;
                                                            }
                                                        </style>

                                                        <script>
                                                            document.querySelectorAll('.option-checkbox').forEach(function(checkbox) {
                                                                checkbox.addEventListener('change', function() {
                                                                    const checkboxes = document.querySelectorAll('.option-checkbox');
                                                                    checkboxes.forEach(function(otherCheckbox) {
                                                                        if (otherCheckbox !== checkbox) {
                                                                            otherCheckbox.checked = false; // Uncheck all other checkboxes
                                                                        }
                                                                    });
                                                                });
                                                            });
                                                        </script>
                                                    @elseif($question->tipe_soal === 'essay')
                                                        <div class="form-group">
                                                            <textarea class="form-control" name="answers[{{ $question->id }}]" rows="4">{{ $cekjawaban->jawaban ? $cekjawaban->jawaban : '' }}</textarea>
                                                        </div>
                                                        
                                                    @endif
                                                    <hr>
                                                        <input class="form-control mb-2" type="number" name="nilai[{{ $cekjawaban->id }}]" value="{{ $cekjawaban->skor ?? 0 }}">
                                                </div>
                                            @endforeach
                                        </div>
                                        <div class="navigation-buttons">
                                            <button type="button" id="prev-btn" class="btn btn-danger">Previous</button>
                                            <button type="button" id="next-btn" class="btn btn-success">Next</button>
                                        </div>
                                        <div class="mt-3">
                                            <button type="submit" id="submit-btn" class="btn btn-primary"
                                                style="float: right">Submit Nilai</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script type="text/javascript">
        $(document).ready(function() {
            let currentQuestionId = null;

            // Show specific question based on button click
            $('.question-btn').click(function() {
                const questionId = $(this).data('id');
                currentQuestionId = questionId;

                // Highlight the current question button
                $('.question-btn').each(function() {
                    if ($(this).data('id') === questionId) {
                        $(this).css('background-color', '#007bff').css('color', '#fff');
                    } else {
                        $(this).css('background-color', '#d6d6d6').css('color', '#000');
                    }
                });

                // Show the clicked question and hide others
                $('.question').hide();
                $('#question-' + questionId).show();
            });

            // Handle 'Next' and 'Previous' button clicks
            $('#next-btn').click(function() {
                const nextQuestion = $('.question-btn[data-id=' + (currentQuestionId + 1) + ']');
                if (nextQuestion.length > 0) {
                    nextQuestion.click();
                }
            });

            $('#prev-btn').click(function() {
                const prevQuestion = $('.question-btn[data-id=' + (currentQuestionId - 1) + ']');
                if (prevQuestion.length > 0) {
                    prevQuestion.click();
                }
            });

            // Handle option button selection
            $(document).ready(function() {
                // Menangani klik pada tombol pilihan
                $(".option-btn").on("click", function() {
                    var button = $(this);
                    var questionId = button.data("question-id");
                    var optionId = button.data("option-id");
                    var checkbox = $("#option-" + questionId + "-" +
                        optionId); // Mencari checkbox yang sesuai

                    // Toggle status checkbox
                    if (checkbox.is(":checked")) {
                        checkbox.prop("checked", false); // Hapus centang
                        button.removeClass("btn-primary").addClass(
                            "btn-outline-primary"); // Ubah gaya tombol
                    } else {
                        checkbox.prop("checked", true); // Centang checkbox
                        button.removeClass("btn-outline-primary").addClass(
                            "btn-primary"); // Ubah gaya tombol
                    }
                });
            });


            // Form validation before submission
            $('#quizForm').submit(function(event) {
                let allAnswered = true;
                $('textarea, input[type="checkbox"]').each(function() {
                    const questionId = $(this).closest('.question').attr('id').replace('question-',
                        '');
                    const questionBtn = $('.question-btn[data-id="' + questionId + '"]');

                    if ($(this).is('textarea') && !$(this).val()) {
                        allAnswered = false;
                        questionBtn.css('background-color', '#d6d6d6');
                    } else if ($(this).is('input[type="checkbox"]') && !$('input[name="answers[' +
                            questionId + '][]"]:checked').length) {
                        allAnswered = false;
                        questionBtn.css('background-color', '#d6d6d6');
                    } else {
                        questionBtn.css('background-color', '#007bff');
                    }
                });

                if (!allAnswered) {
                    event.preventDefault();
                    swal("Error!", "Please answer all questions before submitting.", "error");
                }
            });

            // Default view to the first question
            $('.question-btn').first().click();
        });
    </script>
@endpush
