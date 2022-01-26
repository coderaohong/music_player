<!DOCTYPE html>
<html lang="en">

<head>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Music player</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css"
    integrity="sha512-HK5fgLBL+xu6dm/Ii3z4xhlSUyZgTT9tuc/hSrtw6uzJOvgRr2a9jyxxT1ely+B+xFAmJKVSTbpM/CuL7qxO8w=="
    crossorigin="anonymous" />
  <link rel="preconnect" href="https://fonts.gstatic.com">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="./assets/css/style.css">
</head>

<body>
  <div class="player">
    <!-- Dashboard -->
    <div class="dashboard">
      <!-- Header -->
      <header>
        <h4>Now playing:</h4>
        <h2>String 57th & 9th</h2>
      </header>

      <!-- CD -->
      <div class="cd">
        <div class="cd-thumb" style="background-image: url('./assets/img/song1.jpg')">
        </div>
      </div>

      <!-- Control -->
      <div class="control">
        <div class="btn btn-repeat">
          <i class="fas fa-redo"></i>
        </div>
        <div class="btn btn-prev">
          <i class="fas fa-step-backward"></i>
        </div>
        <div class="btn btn-toggle-play">
          <i class="fas fa-pause icon-pause"></i>
          <i class="fas fa-play icon-play"></i>
        </div>
        <div class="btn btn-next">
          <i class="fas fa-step-forward"></i>
        </div>
        <div class="btn btn-random">
          <i class="fas fa-random"></i>
        </div>
      </div>

      <input id="progress" class="progress" type="range" value="0" step="1" min="0" max="100">

      <audio id="audio" src=""></audio>
    </div>

    <!-- Playlist -->
    <div class="playlist">

    </div>
    <!-- Design from: https://static.collectui.com/shots/3671744/musicloud-revolutionary-app-for-music-streaming-large -->
    <script>
      const $ = document.querySelector.bind(document)
      const $$ = document.querySelectorAll.bind(document)
      const PLAYER_STORAGE_KEY = 'F8_PLAYER'

      const player = $('.player')
      const cd = $('.cd')
      const heading = $('header h2')
      const cdThumb = $('.cd-thumb')
      const audio = $('#audio')
      const playBtn = $('.btn-toggle-play')
      const progress = $('#progress')
      const nextBtn = $('.btn-next')
      const prevBtn = $('.btn-prev')
      const randomBtn = $('.btn-random')
      const repeatBtn = $('.btn-repeat')
      const playlist = $('.playlist')

      const app = {
        currentIndex: 0,
        isPlaying: false,
        isRandom: false,
        isRepeat: false,
        config: JSON.parse(localStorage.getItem(PLAYER_STORAGE_KEY)) || {},
        songs: [
          {
            name: 'Đêm trăng tình yêu',
            singer: 'MGV',
            path: './assets/music/song1.mp3',
            image: './assets/img/song1.jpg'
          },
          {
            name: 'Phố xa',
            singer: 'Tam ca áo trắng',
            path: './assets/music/song2.mp3',
            image: './assets/img/song2.jpg'
          },
          {
            name: 'Tình đơn phương',
            singer: 'Đan Trường',
            path: './assets/music/song3.mp3',
            image: './assets/img/song3.jpg'
          },
          {
            name: 'Kiếp ve sầu',
            singer: 'Đan Trường',
            path: './assets/music/song4.mp3',
            image: './assets/img/song4.jpg'
          },
          {
            name: 'Vầng trăng khóc',
            singer: 'Nhật Tinh Anh ft Khánh Ngọc',
            path: './assets/music/song5.mp3',
            image: './assets/img/song5.jpg'
          },
          {
            name: 'Cầu vồng khuyết',
            singer: 'Tuấn Hưng',
            path: './assets/music/song6.mp3',
            image: './assets/img/song6.jpg'
          }
        ],
        setConfig: function(key, value) {
          this.config[key] = value
          localStorage.setItem(PLAYER_STORAGE_KEY, JSON.stringify(this.config))
        },
        render: function () {
          const htmls = this.songs.map((song, index) => {
            return `
          <div class="song ${index === this.currentIndex ? 'active' : ''}" data-index="${index}">
            <div class="thumb" style="background-image: url('${song.image}')">
            </div>
            <div class="body">
              <h3 class="title">${song.name}</h3>
              <p class="author">${song.singer}</p>
            </div>
            <div class="option">
              <i class="fas fa-ellipsis-h"></i>
            </div>
          </div>
          `
          })
          $('.playlist').innerHTML = htmls.join('');
        },
        defineProperties: function () {
          Object.defineProperty(this, 'currentSong', {
            get: function () {
              return this.songs[this.currentIndex]
            }
          })
        },
        handleEvents: function () {
          const cdWidth = cd.offsetWidth
          const _this = this
          // Xử lí CD quay / dừng
          const cdThumbAnimate = cdThumb.animate([
            { transform: 'rotate(360deg)' }
          ], {
            duration: 10000,
            iterations: Infinity
          })
          cdThumbAnimate.pause()
          //Xử lí phóng to, thu nhỏ
          document.onscroll = function () {
            const scrollTop = window.scrollY || document.documentElement.scrollTop
            const newCdWidth = cdWidth - scrollTop

            cd.style.width = newCdWidth > 0 ? newCdWidth + 'px' : 0
            cd.style.opacity = newCdWidth / cdWidth
          }
          //Xử lí khi click play
          playBtn.onclick = function () {
            if (_this.isPlaying) {
              audio.pause()
            } else {
              audio.play()
            }
          }
          // Khi song được play 
          audio.onplay = function () {
            _this.isPlaying = true;
            player.classList.add('playing')
            cdThumbAnimate.play()
          }
          // Khi song bị pause
          audio.onpause = function () {
            _this.isPlaying = false;
            player.classList.remove('playing')
            cdThumbAnimate.pause()
          }
          // Khi tiến độ bài hát thay đổi 
          audio.ontimeupdate = function () {
            if (audio.duration) {
              const progressPercent = Math.floor(audio.currentTime / audio.duration * 100)
              progress.value = progressPercent
            }
          }
          // Xử lí khi tua
          progress.onchange = function (e) {
            const seekTime = audio.duration / 100 * e.target.value
            audio.currentTime = seekTime
          }
          // Xử lí khi next song
          nextBtn.onclick = function () {
            if (_this.isRandom) {
              _this.playRandomSong()
            } else {
              _this.nextSong()
            }
            audio.play()
            _this.render()
            _this.scrollToActiveSong()
          }
          //
          prevBtn.onclick = function () {
            if (_this.isRandom) {
              _this.playRandomSong()
            } else {
              _this.prevSong()
            }
            audio.play()
            _this.render()
            _this.scrollToActiveSong()
          }
          // Xử lí bật / tắt random song
          randomBtn.onclick = function (e) {
            _this.isRandom = !_this.isRandom
            _this.setConfig('isRandom', _this.isRandom)
            randomBtn.classList.toggle('active', _this.isRandom)
          }
          // Xử lí lặp lại
          repeatBtn.onclick = function (e) {
            _this.isRepeat = !_this.isRepeat
            _this.setConfig('isRepeat', _this.isRepeat)
            repeatBtn.classList.toggle('active', _this.isRepeat)
          }
          // Xử lí next song khi audio ended
          audio.onended = function () {
            if (_this.isRepeat) {
              audio.play()
            } else {
              nextBtn.click()
            }
          }
          // Lắng nghe hành vi click vào playlist
          playlist.onclick = function(e) {
            const songNode = e.target.closest('.song:not(.active)')
            if(songNode || !e.target.closest('.option')) {
              // Xử lí khi click vào song
              if (songNode) {
                _this.currentIndex = Number(songNode.dataset.index)
                _this.loadCurrentSong()
                _this.render()
                audio.play()
              }
            }
          }
        },
        scrollToActiveSong: function() {
          setTimeout(() => {
            $('.song.active').scrollIntoView({
              behavior: 'smooth',
              block: 'nearest'
            })
          }, 300)
        },
        loadCurrentSong: function () {
          heading.textContent = this.currentSong.name
          cdThumb.style.backgroundImage = `url('${this.currentSong.image}')`
          audio.src = this.currentSong.path
        },
        loadConfig: function() {
          this.isRandom = this.config.isRandom
          this.isRepeat = this.config.isRepeat
        },
        nextSong: function () {
          this.currentIndex++
          if (this.currentIndex >= this.songs.length) {
            this.currentIndex = 0
          }
          this.loadCurrentSong()
        },
        prevSong: function () {
          this.currentIndex--
          if (this.currentIndex < 0) {
            this.currentIndex = this.songs.length - 1
          }
          this.loadCurrentSong()
        },
        playRandomSong: function () {
          let newIndex
          do {
            newIndex = Math.floor(Math.random() * this.songs.length)
          } while (newIndex === this.currentIndex)
          this.currentIndex = newIndex
          this.loadCurrentSong()
        },
        start: function () {
          // Gán cấu hình từ config vào ứng dụng
          this.loadConfig()

          // Định nghĩa các thuộc tính cho object
          this.defineProperties()

          // Lắng nghe, xử lí các sự kiện
          this.handleEvents()

          // Tải thông tin bài hát đầu tin vào UI khi chạy ứng dụng
          this.loadCurrentSong()

          // Render playlist
          this.render()

          // Hiển thị trạng thái ban đầu của button repeat và random
          randomBtn.classList.toggle('active', this.isRandom)
          repeatBtn.classList.toggle('active', this.isRepeat)
        }
      }
      app.start()
    </script>
</body>

</html>