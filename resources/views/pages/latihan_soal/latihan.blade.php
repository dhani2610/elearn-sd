@extends('layouts.main')
@section('title', 'Ujian Online')

@section('content')
{{-- <style>
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
        /* background-color: #f8f9fa; */
        background-color: #0056b3!important;
        border: 2px solid #007bff;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s, transform 0.3s;
    }

    .option-checkbox:checked+label .option-btn {
        background-color: #007bff;
        color: white;
        transform: scale(1.1);
    }

    .option-checkbox:checked+label .option-btn:active {
        transform: scale(1);
        color: white;
        background-color: #0056b3!important;
    }

    .option-checkbox:checked+label .option-btn:hover {
        color: white;
        background-color: #0056b3;
    }
    .option-btn:active {
        color: white!important;
        background-color: #0056b3!important;
    }
</style> --}}

    <section class="section custom-section">
        <div class="section-body">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between">
                            <h4>Latihan Soal - {{ $questions->judul }}</h4>
                        </div>
                        <div class="card-body">
                            <form id="quizForm" action="{{ route('kirim-jawaban-latihan', $questions->id) }}" method="POST">
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
                                            @foreach ($questions->soals as $key => $question)
                                                <div class="question" id="question-{{ $question->id }}" style="display: none;">
                                                    <h5>{{ $question->pertanyaan }}</h5>
                                                    @if ($question->tipe_soal === 'pilihan_ganda')
                                                        <div class="form-group">
                                                            @php $pil = 'A'; @endphp
                                                            @foreach ($question->pilihan as $optionKey => $option)
                                                                <div class="option-container">
                                                                    <input type="radio" class="option-radio"
                                                                        name="answers[{{ $question->id }}]"
                                                                        value="Opsi {{ $pil }}"
                                                                        id="option-{{ $question->id }}-{{ $optionKey }}"
                                                                        {{ isset($question->answered) && $question->answered == $optionKey ? 'checked' : '' }}>
                                        
                                                                    <label for="option-{{ $question->id }}-{{ $optionKey }}">
                                                                        <button class="option-btn btn btn-primary" type="button">
                                                                            {{ $pil++ }}. {{ $option }}
                                                                        </button>
                                                                    </label>
                                                                </div>
                                                            @endforeach
                                                        </div>
                                                    @elseif($question->tipe_soal === 'essay')
                                                        <div class="form-group">
                                                            <textarea class="form-control" name="answers[{{ $question->id }}]" rows="4">{{ $question->answered ? $question->answered_text : '' }}</textarea>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                        
                                        <div class="navigation-buttons">
                                            <button type="button" id="prev-btn" class="btn btn-danger">Previous</button>
                                            <button type="button" id="next-btn" class="btn btn-success">Next</button>
                                        </div>
                                        <div class="mt-3">
                                            <button type="submit" id="submit-btn" class="btn btn-primary"
                                                style="float: right">Submit Jawaban</button>
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


            $('#quizForm').submit(function(event) {
                let allAnswered = true;

                $('.question').each(function() {
                    const $question = $(this);
                    const questionId = $question.attr('id').replace('question-', '');
                    const questionBtn = $('.question-btn[data-id="' + questionId + '"]');
                    console.log();
                    
                    // Cek apakah pertanyaan bertipe checkbox atau textarea
                    if ($question.find('input[type="checkbox"]').length > 0) {
                        // Validasi untuk pilihan ganda (checkbox)
                        if ($question.find('input[type="checkbox"]:checked').length === 0) {
                            allAnswered = false;
                            questionBtn.css('background-color', '#d6d6d6'); // Indikator error
                        } else {
                            questionBtn.css('background-color', '#007bff'); // Indikator valid
                        }
                    } else if ($question.find('textarea').length > 0) {
                        // Validasi untuk soal essay (textarea)
                        const answer = $question.find('textarea').val().trim();
                        if (answer === '') {
                            allAnswered = false;
                            questionBtn.css('background-color', '#d6d6d6'); // Indikator error
                        } else {
                            questionBtn.css('background-color', '#007bff'); // Indikator valid
                        }
                    }
                });

                console.log('All answered:', allAnswered);

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
