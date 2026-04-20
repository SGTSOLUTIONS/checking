<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tamil Movie Quiz Challenge</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1a2a6c, #b21f1f, #fdbb2d);
            color: white;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            width: 100%;
            background: rgba(0, 0, 0, 0.7);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            text-align: center;
        }

        h1 {
            font-size: 2.5rem;
            margin-bottom: 10px;
            background: linear-gradient(to right, #ff9966, #ff5e62);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .subtitle {
            font-size: 1.2rem;
            margin-bottom: 30px;
            color: #cccccc;
        }

        .progress-container {
            width: 100%;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            margin-bottom: 30px;
            overflow: hidden;
        }

        .progress-bar {
            height: 10px;
            background: linear-gradient(to right, #ff9966, #ff5e62);
            width: 0%;
            transition: width 0.5s ease;
        }

        .question-container {
            margin-bottom: 30px;
        }

        .question {
            font-size: 1.8rem;
            margin-bottom: 20px;
            color: #ffcc00;
        }

        .options {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-bottom: 20px;
        }

        .option {
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 10px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 1.1rem;
        }

        .option:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }

        .option.correct {
            background: rgba(76, 175, 80, 0.3);
            border-color: #4CAF50;
        }

        .option.wrong {
            background: rgba(244, 67, 54, 0.3);
            border-color: #F44336;
        }

        .hint-container {
            margin-bottom: 20px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            font-size: 1rem;
        }

        .hint-title {
            font-weight: bold;
            margin-bottom: 5px;
            color: #ffcc00;
        }

        .next-btn {
            background: linear-gradient(to right, #ff9966, #ff5e62);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .next-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(255, 94, 98, 0.4);
        }

        .next-btn:disabled {
            background: #666;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .result-container {
            display: none;
            margin-top: 30px;
        }

        .result-title {
            font-size: 2rem;
            margin-bottom: 20px;
            color: #ffcc00;
        }

        .score {
            font-size: 3rem;
            margin-bottom: 20px;
            color: #4CAF50;
        }

        .restart-btn {
            background: linear-gradient(to right, #00b09b, #96c93d);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 12px 30px;
            font-size: 1.2rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .restart-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(0, 176, 155, 0.4);
        }

        .clue {
            font-style: italic;
            color: #ffcc00;
            margin-top: 10px;
        }

        .search-btn {
            background: linear-gradient(to right, #4A00E0, #8E2DE2);
            color: white;
            border: none;
            border-radius: 50px;
            padding: 10px 20px;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-top: 10px;
        }

        .search-btn:hover {
            transform: scale(1.05);
            box-shadow: 0 5px 15px rgba(142, 45, 226, 0.4);
        }

        @media (max-width: 600px) {
            .options {
                grid-template-columns: 1fr;
            }

            h1 {
                font-size: 2rem;
            }

            .question {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Tamil Movie Quiz Challenge</h1>
        <p class="subtitle">Test your knowledge of Tamil movies from 2000-2025!</p>

        <div class="progress-container">
            <div class="progress-bar" id="progressBar"></div>
        </div>

        <div class="question-container" id="questionContainer">
            <div class="question" id="question">Loading question...</div>
            <div class="options" id="options">
                <!-- Options will be inserted here by JavaScript -->
            </div>
            <div class="hint-container" id="hintContainer">
                <div class="hint-title">Clue:</div>
                <div id="hintText">Use your browser to search for clues!</div>
                <div class="clue" id="clueText"></div>
                <button class="search-btn" id="searchBtn">Search Online for Clues</button>
            </div>
            <button class="next-btn" id="nextBtn" disabled>Next Question</button>
        </div>

        <div class="result-container" id="resultContainer">
            <div class="result-title">Quiz Completed!</div>
            <div class="score" id="score">0/0</div>
            <p>Congratulations on completing the Tamil Movie Quiz Challenge!</p>
            <button class="restart-btn" id="restartBtn">Play Again</button>
        </div>
    </div>

    <script>
        // Tamil movie database for generating questions (2000-2025)
        const tamilMovies = [
            {
                movie: "Kaakha Kaakha",
                year: 2003,
                hero: "Suriya",
                heroine: "Jyothika",
                song: "Ennai Konjam",
                director: "Gautham Vasudev Menon"
            },
            {
                movie: "Ghajini",
                year: 2005,
                hero: "Suriya",
                heroine: "Asin",
                song: "Oru Maalai",
                director: "A.R. Murugadoss"
            },
            {
                movie: "Vettaiyaadu Vilaiyaadu",
                year: 2006,
                hero: "Kamal Haasan",
                heroine: "Jyothika",
                song: "Manjal Veyyil",
                director: "Gautham Vasudev Menon"
            },
            {
                movie: "Sivaji",
                year: 2007,
                hero: "Rajinikanth",
                heroine: "Shreya Saran",
                song: "Balleilakka",
                director: "S. Shankar"
            },
            {
                movie: "Ayan",
                year: 2009,
                hero: "Suriya",
                heroine: "Tamannaah",
                song: "Pala Pala",
                director: "K.V. Anand"
            },
            {
                movie: "Enthiran",
                year: 2010,
                hero: "Rajinikanth",
                heroine: "Aishwarya Rai",
                song: "Kilimanjaro",
                director: "S. Shankar"
            },
            {
                movie: "Mankatha",
                year: 2011,
                hero: "Ajith Kumar",
                heroine: "Lakshmi Rai",
                song: "Vilayaadu Mankatha",
                director: "Venkat Prabhu"
            },
            {
                movie: "3",
                year: 2012,
                hero: "Dhanush",
                heroine: "Shruti Haasan",
                song: "Why This Kolaveri Di",
                director: "Aishwarya R. Dhanush"
            },
            {
                movie: "Thuppakki",
                year: 2012,
                hero: "Vijay",
                heroine: "Kajal Aggarwal",
                song: "Google Google",
                director: "A.R. Murugadoss"
            },
            {
                movie: "Vishwaroopam",
                year: 2013,
                hero: "Kamal Haasan",
                heroine: "Pooja Kumar",
                song: "Unnai Kaanadhu",
                director: "Kamal Haasan"
            },
            {
                movie: "Kaththi",
                year: 2014,
                hero: "Vijay",
                heroine: "Samantha",
                song: "Selfie Pulla",
                director: "A.R. Murugadoss"
            },
            {
                movie: "I",
                year: 2015,
                hero: "Vikram",
                heroine: "Amy Jackson",
                song: "Mersalaayitten",
                director: "S. Shankar"
            },
            {
                movie: "Kabali",
                year: 2016,
                hero: "Rajinikanth",
                heroine: "Radhika Apte",
                song: "Neruppu Da",
                director: "Pa. Ranjith"
            },
            {
                movie: "Vikram Vedha",
                year: 2017,
                hero: "Vijay Sethupathi",
                heroine: "Shraddha Srinath",
                song: "Yaanji",
                director: "Pushkar-Gayathri"
            },
            {
                movie: "Mersal",
                year: 2017,
                hero: "Vijay",
                heroine: "Kajal Aggarwal",
                song: "Aalaporan Tamizhan",
                director: "Atlee"
            },
            {
                movie: "2.0",
                year: 2018,
                hero: "Rajinikanth",
                heroine: "Amy Jackson",
                song: "Endhira Logathu",
                director: "S. Shankar"
            },
            {
                movie: "Petta",
                year: 2019,
                hero: "Rajinikanth",
                heroine: "Simran",
                song: "Marana Mass",
                director: "Karthik Subbaraj"
            },
            {
                movie: "Bigil",
                year: 2019,
                hero: "Vijay",
                heroine: "Nayanthara",
                song: "Verithanam",
                director: "Atlee"
            },
            {
                movie: "Master",
                year: 2021,
                hero: "Vijay",
                heroine: "Malavika Mohanan",
                song: "Vaathi Coming",
                director: "Lokesh Kanagaraj"
            },
            {
                movie: "Jai Bhim",
                year: 2021,
                hero: "Suriya",
                heroine: "Lijomol Jose",
                song: "Vaanam",
                director: "T.J. Gnanavel"
            },
            {
                movie: "Vikram",
                year: 2022,
                hero: "Kamal Haasan",
                heroine: "Gayathrie",
                song: "Pathala Pathala",
                director: "Lokesh Kanagaraj"
            },
            {
                movie: "Ponniyin Selvan: I",
                year: 2022,
                hero: "Vikram",
                heroine: "Aishwarya Rai",
                song: "Chola Chola",
                director: "Mani Ratnam"
            },
            {
                movie: "Varisu",
                year: 2023,
                hero: "Vijay",
                heroine: "Rashmika Mandanna",
                song: "Ranja Ranja",
                director: "Vamshi Paidipally"
            },
            {
                movie: "Jailer",
                year: 2023,
                hero: "Rajinikanth",
                heroine: "Ramya Krishnan",
                song: "Kaavaalaa",
                director: "Nelson Dilipkumar"
            },
            {
                movie: "Leo",
                year: 2023,
                hero: "Vijay",
                heroine: "Trisha",
                song: "Naa Ready",
                director: "Lokesh Kanagaraj"
            },
            {
                movie: "Captain Miller",
                year: 2024,
                hero: "Dhanush",
                heroine: "Shiva Rajkumar",
                song: "Killer Killer",
                director: "Arun Matheswaran"
            }
        ];

        // DOM elements
        const questionElement = document.getElementById('question');
        const optionsElement = document.getElementById('options');
        const hintTextElement = document.getElementById('hintText');
        const clueTextElement = document.getElementById('clueText');
        const nextButton = document.getElementById('nextBtn');
        const progressBar = document.getElementById('progressBar');
        const questionContainer = document.getElementById('questionContainer');
        const resultContainer = document.getElementById('resultContainer');
        const scoreElement = document.getElementById('score');
        const restartButton = document.getElementById('restartBtn');
        const searchButton = document.getElementById('searchBtn');

        // Quiz state
        let currentQuestionIndex = 0;
        let score = 0;
        let selectedOption = null;
        let quizQuestions = [];

        // Initialize the quiz
        function initQuiz() {
            generateQuestions();
            showQuestion();
            updateProgressBar();

            // Add event listeners
            nextButton.addEventListener('click', nextQuestion);
            restartButton.addEventListener('click', restartQuiz);
            searchButton.addEventListener('click', searchOnline);
        }

        // Generate random questions
        function generateQuestions() {
            quizQuestions = [];

            // Generate 5 random questions
            for (let i = 0; i < 5; i++) {
                const questionType = Math.floor(Math.random() * 3);
                const movie = tamilMovies[Math.floor(Math.random() * tamilMovies.length)];

                if (questionType === 0) {
                    // Hero + Song question
                    const otherHeroes = tamilMovies
                        .filter(m => m.hero !== movie.hero)
                        .sort(() => 0.5 - Math.random())
                        .slice(0, 3)
                        .map(m => m.hero);

                    const options = [movie.hero, ...otherHeroes].sort(() => 0.5 - Math.random());
                    const correctAnswer = options.indexOf(movie.hero);

                    quizQuestions.push({
                        question: `Who played the hero in "${movie.movie}" (${movie.year}) which featured the song "${movie.song}"?`,
                        options: options,
                        correctAnswer: correctAnswer,
                        hint: `This actor also starred in ${movie.movie} with ${movie.heroine}.`,
                        clue: `Search: "${movie.movie} hero" or "${movie.song} movie"`
                    });
                } else if (questionType === 1) {
                    // Heroine + Song question
                    const otherHeroines = tamilMovies
                        .filter(m => m.heroine !== movie.heroine)
                        .sort(() => 0.5 - Math.random())
                        .slice(0, 3)
                        .map(m => m.heroine);

                    const options = [movie.heroine, ...otherHeroines].sort(() => 0.5 - Math.random());
                    const correctAnswer = options.indexOf(movie.heroine);

                    quizQuestions.push({
                        question: `Who played the heroine in "${movie.movie}" (${movie.year}) which featured the song "${movie.song}"?`,
                        options: options,
                        correctAnswer: correctAnswer,
                        hint: `This actress starred opposite ${movie.hero} in ${movie.movie}.`,
                        clue: `Search: "${movie.movie} heroine" or "${movie.song} movie actress"`
                    });
                } else {
                    // Movie + Song question
                    const otherMovies = tamilMovies
                        .filter(m => m.movie !== movie.movie)
                        .sort(() => 0.5 - Math.random())
                        .slice(0, 3)
                        .map(m => m.movie);

                    const options = [movie.movie, ...otherMovies].sort(() => 0.5 - Math.random());
                    const correctAnswer = options.indexOf(movie.movie);

                    quizQuestions.push({
                        question: `Which Tamil movie (${movie.year}) starring ${movie.hero} and ${movie.heroine} featured the song "${movie.song}"?`,
                        options: options,
                        correctAnswer: correctAnswer,
                        hint: `This movie was directed by ${movie.director}.`,
                        clue: `Search: "${movie.hero} ${movie.heroine} movie" or "${movie.song} tamil movie"`
                    });
                }
            }
        }

        // Display current question
        function showQuestion() {
            const currentQuestion = quizQuestions[currentQuestionIndex];
            questionElement.textContent = currentQuestion.question;
            hintTextElement.textContent = currentQuestion.hint;
            clueTextElement.textContent = currentQuestion.clue;

            // Clear previous options
            optionsElement.innerHTML = '';

            // Create option buttons
            currentQuestion.options.forEach((option, index) => {
                const optionElement = document.createElement('div');
                optionElement.classList.add('option');
                optionElement.textContent = option;
                optionElement.dataset.index = index;
                optionElement.addEventListener('click', selectOption);
                optionsElement.appendChild(optionElement);
            });

            // Reset next button
            nextButton.disabled = true;
            selectedOption = null;
        }

        // Handle option selection
        function selectOption(event) {
            // Remove selected class from all options
            document.querySelectorAll('.option').forEach(option => {
                option.classList.remove('selected');
            });

            // Add selected class to clicked option
            event.target.classList.add('selected');
            selectedOption = parseInt(event.target.dataset.index);

            // Enable next button
            nextButton.disabled = false;
        }

        // Move to next question
        function nextQuestion() {
            // Check if answer is correct
            const currentQuestion = quizQuestions[currentQuestionIndex];
            const options = document.querySelectorAll('.option');

            // Disable all options
            options.forEach(option => {
                option.removeEventListener('click', selectOption);
            });

            // Show correct/incorrect
            options.forEach((option, index) => {
                if (index === currentQuestion.correctAnswer) {
                    option.classList.add('correct');
                } else if (index === selectedOption && selectedOption !== currentQuestion.correctAnswer) {
                    option.classList.add('wrong');
                }
            });

            // Update score if correct
            if (selectedOption === currentQuestion.correctAnswer) {
                score++;
            }

            // Wait a moment then move to next question or show results
            setTimeout(() => {
                currentQuestionIndex++;

                if (currentQuestionIndex < quizQuestions.length) {
                    showQuestion();
                    updateProgressBar();
                } else {
                    showResults();
                }
            }, 1500);
        }

        // Update progress bar
        function updateProgressBar() {
            const progress = ((currentQuestionIndex + 1) / quizQuestions.length) * 100;
            progressBar.style.width = `${progress}%`;
        }

        // Show final results
        function showResults() {
            questionContainer.style.display = 'none';
            resultContainer.style.display = 'block';
            scoreElement.textContent = `${score}/${quizQuestions.length}`;
        }

        // Restart the quiz
        function restartQuiz() {
            currentQuestionIndex = 0;
            score = 0;
            questionContainer.style.display = 'block';
            resultContainer.style.display = 'none';
            generateQuestions();
            showQuestion();
            updateProgressBar();
        }

        // Search online for clues
        function searchOnline() {
            const currentQuestion = quizQuestions[currentQuestionIndex];
            const searchQuery = currentQuestion.clue.replace('Search: ', '');
            window.open(`https://www.google.com/search?q=${encodeURIComponent(searchQuery)}`, '_blank');
        }

        // Initialize the quiz when page loads
        window.addEventListener('DOMContentLoaded', initQuiz);
    </script>
</body>
</html>
