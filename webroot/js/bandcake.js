Dropzone.options.bandcakeUpload = {
  init: function () {
    this.on('addedfile', function (file) { console.dir('Added file.') })
    this.on('success', function (file, response) {
      if (response !== null) {
        if (undefined === response.file) {
          console.error('No file property in response ;-/')
          return
        }
        if (undefined === response.suggestions) {
          console.error('No suggestions property in response ;-/')
          return
        }
        if (undefined === response.suggestions.songs) {
          console.error('No suggestions.songs property in response ;-/')
          return
        }

        // console.log('Here\'s some songs that this file might belong to:');
        // console.dir(response.suggestions.songs);
        const select = jQuery('<select id="file-song-select-' + response.file + '" style="height: 2em; padding: 2px;" />')
        select.append('<option />')
        jQuery.each(response.suggestions.songs, function (key, song) {
          select.append('<option value="' + song.id + '">' + song.title + '</option>')
        })
        select.on('change', function () {
          // console.log('Selected song ' + this.value + ' for file ' + file);
          jQuery(this).closest('.dz-preview').find('.dz-image').css('background', 'inherit')
          postEditFile(response.file, { song_id: this.value }, function () {
            jQuery('#file-song-select-' + response.file).closest('.dz-preview').find('.dz-image').css('background', '#c0ffc0')
          })
        })
        jQuery(file.previewElement).find('.dz-details').append(select)
      }
    })
  }
}

$(document).ready(function () {
  // console.log('initCalendar()');
  initCalendar()
  // console.log('initDatefilters()');
  initDatefilters()
  // console.log('initSelectize()');
  initSelectize()
  // console.log('initVotes()');
  // initVotes();
  // console.log('initAudioplayer()');
  initAudioplayer()
  // console.log('initComments()');
  initComments()
  // console.log('initFoundation()');
  initFoundation()
})

function changeDateFields () {
  // console.log('changeDateFields() called!')
  $('input.date').each(function (index) {
    let date = this.value
    const type = $(this).attr('type')

    if (type === 'datetime-local') {
      // console.log('change from datetime-local to date')
      this.type = 'date'
      if (date) {
        date = date.substring(0, 10)
        this.value = date
      }
    } else {
      // console.log('change from date to datetime-local')
      this.type = 'datetime-local'
      if (date) {
        date = date + ' 00:00'
        this.value = date
      }
    }
  })
}

function initFoundation () {
  $(document).foundation()
}

function vote (el, value) {
  ul = $(el).closest('ul')
  url = ul.data('url-vote')
  relatedTo = ul.data('related-to').split(':')
  userId = ul.data('user-id')

  if (relatedTo[0] == 'date') { data = { date_id: relatedTo[1], user_id: userId, vote: value } }
  if (relatedTo[0] == 'idea') { data = { idea_id: relatedTo[1], user_id: userId, vote: value } }

  $.ajax({
    type: 'POST',
    url,
    headers: {
      'X-CSRF-Token': ul.data('csrf-token')
    },
    data,
    success: function (data, textStatus, jqXHR) {
      if (jqXHR.status == 200) {
        // console.log('New vote id: ' + data.vote.id)
        // console.dir(jqXHR);
        myVote = $('#my-vote')
        myVote.removeClass('success alert secondary')

        if (data.vote.vote == 1) {
          myVote.addClass('success')
        }
        if (data.vote.vote == -1) {
          myVote.addClass('alert')
        }
        if (data.vote.vote == 0) {
          myVote.addClass('secondary')
        }
      }
    },
    error: function (jqXHR) {
      if (jqXHR.status !== 200) {
        alert(jqXHR.statusText + '\n\n' + jqXHR.responseJSON.message)
      }
      console.dir(jqXHR)
    },
    dataType: 'json'
  })
}

function setVersion (el, collectionId, songId, versionId) {
  ul = $(el).closest('ul')
  url = ul.data('url-set-version')

  $.ajax({
    type: 'POST',
    url,
    headers: {
      'X-CSRF-Token': ul.data('csrf-token')
    },
    data: {
      collection_id: collectionId,
      song_id: songId,
      song_version_id: versionId
    },
    success: function (data, textStatus, jqXHR) {
      if (jqXHR.status == 200) {
        const songId = data.collectionSong.song_id
        const versionId = data.collectionSong.song_version_id
        const versionTitle = $('#song-version-' + versionId).text()
        // console.log('New song_version_id: ' + versionId);
        // console.log('New song_version_title: ' + versionTitle);
        $('#song-' + songId + '-version-title').text(versionTitle)
        if (versionId === null) {
          $('#song-' + songId + '-version-title').parent().addClass('secondary')
        } else {
          $('#song-' + songId + '-version-title').parent().removeClass('secondary')
        }
      }
    },
    error: function (jqXHR) {
      if (jqXHR.status !== 200) {
        alert(jqXHR.statusText + '\n\n' + jqXHR.responseJSON.message)
      }
      console.dir(jqXHR)
    },
    dataType: 'json'
  })
}

function initCalendar () {
  // prepare draggable events
  $('#external-events .fc-event').each(function () {
    // store data so the calendar knows to render an event upon drop
    $(this).data('event-template', {
      title: $.trim($(this).text()), // use the element's text as the event title
      start: $(this).data('start-time'),
      end: $(this).data('stop-time'),
      user: $('#calendar').data('current-user'),
      // editable: true,
      stick: true // maintain when user navigates (see docs on the renderEvent method)
    })

    // make the event draggable using jQuery UI
    $(this).draggable({
      zIndex: 999,
      revert: true, // will cause the event to go back to its
      revertDuration: 0 //  original position after the drag
    })
  })

  $('#calendar').fullCalendar({
    lang: 'de',
    timeFormat: '(HH:mm)',
    weekNumbers: true,
    header: {
      left: 'prev,next today',
      center: 'title',
      right: 'month,agendaWeek' // 'agendaDay'
    },
    height: 'auto',
    selectable: true,
    selectHelper: true,
    select: function (start, end) {
      console.dir('select')

      const title = prompt('Event Title:')
      if (title) {
        const event = prepareNewEvent(this)
        const eventId = event.id
        event.title = title
        event.start = start
        event.end = end
        date = $('#calendar').fullCalendar('renderEvent', event, true) // stick? = true

        data = prepareNewDatePayload(this, date)

        postNewDate(data, eventId)
      }
      $('#calendar').fullCalendar('unselect')
    },
    droppable: true,
    drop: function (date, dropEvent, dropItem, dropTarget) {
      console.log('drop')

      const event = prepareNewEvent(this, date)
      const eventId = event.id

      // render the event on the calendar
      // the last `true` argument determines if the event "sticks" (http://arshaw.com/fullcalendar/docs/event_rendering/renderEvent/)
      $('#calendar').fullCalendar('renderEvent', event, true)

      data = prepareNewDatePayload(this, date)

      postNewDate(data, eventId)
    },
    eventDrop: function (date, delta, x, dropEvent) {
      console.dir('eventDrop')
    },
    events: $('#calendar').data('events')
  })
}

function prepareNewEvent (cal, date) {
  let event = {}
  event.id = Math.random().toString(36).substr(2, 10)
  event.className = 'unsafed-event'

  if (typeof date === 'object') {
    // console.dir(date);
    // console.dir(date._d);
    // console.dir(date._d.toUTCString());
    // console.dir(dropEvent);
    // console.dir(dropItem);
    // console.dir(dropTarget);
    // console.dir(jQuery(this).data('event-data'));

    const eventTemplate = $(cal).data('event-template')
    // console.dir(eventTemplate);

    // we need to copy it, so that multiple events don't have a reference to the same object
    event = $.extend(event, eventTemplate)
    event.start = moment(date._d.toDateString() + ' ' + event.start)
    event.end = moment(date._d.toDateString() + ' ' + event.end)
  }

  return event
}

function prepareNewDatePayload (cal, date) {
  const eventTemplate = $(cal).data('event-template')

  if (typeof eventTemplate === 'object') {
    data = {
      user_id: eventTemplate.user,
      title: eventTemplate.title,
      begin: date._d.toMyDateString() + ' ' + eventTemplate.start.substring(0, 2) + ':' + eventTemplate.start.substring(3, 5),
      end: date._d.toMyDateString() + ' ' + eventTemplate.end.substring(0, 2) + ':' + eventTemplate.end.substring(3, 5),
      is_fullday: 0,
      status: 0,
      text: ''
    }
  } else {
    date = date[0]
    data = {
      user_id: $('#calendar').data('current-user'),
      title: date.title,
      begin: date.start._d.toMyDateString() + ' 00:00',
      end: date.start._d.toMyDateString() + ' 23:59',
      is_fullday: 1,
      status: 0,
      text: ''
    }
  }

  return data
}

function postNewDate (data, eventId) {
  $.ajax({
    type: 'POST',
    url: $('#calendar').data('url-add'),
    headers: {
      'X-CSRF-Token': $('#calendar').data('csrf-token')
    },
    data,
    success: function (data, textStatus, jqXHR) {
      if (jqXHR.status == 200) {
        console.log('New date_id: ' + data.date.id)
        // console.dir(jqXHR);

        // get event
        let event = $('#calendar').fullCalendar('clientEvents', eventId)
        if (event.length) {
          // set url and stop the spinner
          event = event[0]
          event.id = data.date.id
          event.url = $('#calendar').data('url-view') + '/' + data.date.id
          event.className = ''

          // save event
          $('#calendar').fullCalendar('updateEvent', event)
        }
      }
    },
    dataType: 'json'
  })
}

function initComments () {
  const commentTable = $('table.comments')
  const togglePanel = commentTable.find('.toggle-old-ones')
  const showOldOnes = commentTable.find('.toggle-old-ones a.show-old-ones')
  const hideOldOnes = commentTable.find('.toggle-old-ones a.hide-old-ones')

  let oldComments = commentTable.find('tr.older-than-last-month')
  const newComments = commentTable.find('tr.younger-than-last-month')

  if (newComments.length == 0) {
    // in case there's no new comments, remove the latest from the list of old ones,
    // so it's not gonna be hidden.
    lastOldComment = oldComments.last()
    lastOldComment.removeClass('hide')
    oldComments = oldComments.not(lastOldComment)
  }

  if (oldComments.length > 0) {
    togglePanel.removeClass('hide')
    showOldOnes.on('click', function () {
      oldComments.removeClass('hide')
      showOldOnes.addClass('hide')
      hideOldOnes.removeClass('hide')
    })
    hideOldOnes.on('click', function () {
      oldComments.addClass('hide')
      showOldOnes.removeClass('hide')
      hideOldOnes.addClass('hide')
    })
  }
}

function initSelectize () {
  // https://selectize.dev/docs/demos
  $('select[multiple]').selectize({
    plugins: ['remove_button', 'drag_drop'],
    onDelete: function (values) {
      if (values.length > 1) {
        message = 'Are you sure you want to remove these ' + values.length + ' items?';
      } else {
        item = $('[data-value=' + values[0] + ']');
        label = item.clone().children().remove().end().text();
        message = 'Are you sure you want to remove "' + label + '"?';
      }
      return confirm(message);
    }
  })

  // Handle touch interactions for selectize items in capture phase, before touch-punch
  // gets to see them. This gives us full control over both remove (tap .remove) and
  // drag-to-reorder (drag .item text), without relying on touch-punch's mouse simulation.
  $('select[multiple]').each(function () {
    var selectize = this.selectize || $(this).data('selectize')
    if (!selectize || !selectize.$control) return
    var $control = selectize.$control
    var control = $control[0]
    var drag = null

    control.addEventListener('touchstart', function (e) {
      var $item = $(e.target).closest('[data-value]')
      if (!$item.length) return
      // Block touch-punch from seeing this touch entirely
      e.stopImmediatePropagation()
      e.preventDefault()
      if ($(e.target).closest('.remove').length) return // handled on touchend
      drag = {
        $el: $item,
        startX: e.touches[0].clientX,
        startY: e.touches[0].clientY,
        started: false
      }
    }, true)

    control.addEventListener('touchmove', function (e) {
      if (!drag) return
      e.preventDefault()
      var touch = e.touches[0]
      var dx = touch.clientX - drag.startX
      var dy = touch.clientY - drag.startY
      if (!drag.started && Math.sqrt(dx * dx + dy * dy) > 5) {
        drag.started = true
        drag.$el.addClass('ui-sortable-helper')
      }
      if (!drag.started) return
      // Re-position item based on where finger is
      $control.children('[data-value]').not(drag.$el).each(function () {
        var r = this.getBoundingClientRect()
        if (touch.clientY >= r.top && touch.clientY <= r.bottom) {
          if (touch.clientX < r.left + r.width / 2) {
            $(this).before(drag.$el)
          } else {
            $(this).after(drag.$el)
          }
          return false
        }
      })
    }, {passive: false})

    control.addEventListener('touchend', function (e) {
      // Handle .remove tap
      var $remove = $(e.target).closest('.remove')
      if ($remove.length) {
        e.stopImmediatePropagation()
        e.preventDefault()
        if (!selectize.isLocked) {
          var $item = $remove.closest('[data-value]')
          var value = $item.attr('data-value')
          var label = $item.clone().children().remove().end().text().trim()
          if (confirm('Are you sure you want to remove "' + label + '"?')) {
            selectize.removeItem(value)
          }
        }
        return
      }
      // Handle drag end
      if (!drag) return
      if (drag.started) {
        drag.$el.removeClass('ui-sortable-helper')
        var values = []
        $control.children('[data-value]').each(function () {
          values.push($(this).attr('data-value'))
        })
        selectize.isFocused = false
        selectize.setValue(values)
        selectize.isFocused = true
      }
      drag = null
    }, true)
  })
}

if (!Date.prototype.toMyDateString) {
  (function () {
    function pad (number) {
      if (number < 10) {
        return '0' + number
      }
      return number
    }

    Date.prototype.toMyDateString = function () {
      return this.getUTCFullYear() +
  '-' + pad(this.getUTCMonth() + 1) +
  '-' + pad(this.getUTCDate())
    }
  }())
}

let wavesurfer

function initWaveform () {
  if (undefined === wavesurfer) {
    wavesurfer = WaveSurfer.create({
      // https://wavesurfer-js.org/docs/options.html
      container: '#audioplayer .waveform',
      waveColor: 'gray',
      progressColor: 'blue',
      height: 50,
      normalize: false,
      splitChannels: false,
      interact: true, // mouse interaction
      backend: 'MediaElement',
      plugins: [
        WaveSurfer.cursor.create({
          showTime: true,
          opacity: 1,
          customShowTimeStyle: {
            'background-color': '#000',
            color: '#fff',
            padding: '2px',
            'font-size': '10px'
          }
        }),
        WaveSurfer.timeline.create({
          container: '#audioplayer .timeline'
        }),
        WaveSurfer.regions.create({
        })
      ]
    })
    wavesurfer.on('error', function (e) {
      console.warn(e)
    })
    wavesurfer.on('ready', function () {
      wavesurfer.play()
    })
    $('[data-action="waveform-playPause"]').on('click', function () {
      wavesurfer.playPause()
    })
    $('[data-action="waveform-hide"]').on('click', function () {
      wavesurfer.cancelAjax()
      wavesurfer.stop()
      updateFileRegions()
      $('#audioplayer').hide()
      $('body').removeClass('audioplayer-visible')
    })
    $('[data-action="waveform-edit"]').on('click', function () {
      if ($('#audioplayer').attr('data-file-unlocked') == 0) {
        $('#audioplayer').attr('data-file-unlocked', 1)
        wavesurfer.enableDragSelection({})
      } else {
        $('#audioplayer').attr('data-file-unlocked', 0)
        wavesurfer.disableDragSelection({})
        updateFileRegions()
      }
    })
    wavesurfer.on('region-update-end', function() {
      $('#audioplayer').attr('data-file-dirty', true)
    })
    wavesurfer.on('region-dblclick', function (region) {
      let oldTitle = region.attributes.title
      let newTitle = prompt('Set new title? (currently: ' + oldTitle + ')')
      if (newTitle !== null) {
        region.attributes.title = newTitle
      }
    })
    $('body').on('keydown', function (event) {
      // console.log($(':focus').length);
      // console.log($(':focus'));
      if ($(':focus').length > 0) {
        return
      }
      // console.log(event.keyCode);
      if (event.keyCode == 27) {
        $('[data-action="waveform-hide"]:visible').trigger('click')
      }
      if (event.keyCode == 32) {
        event.preventDefault()
        $('[data-action="waveform-playPause"]:visible').trigger('click')
      }
    })
  }
  $('body').addClass('audioplayer-visible')
  $('#audioplayer').show()
}

function playInWaveform (el) {
  initWaveform()

  const data = $(el).data('audioplayer')

  $('#audioplayer').data('file-id', data.id)
  $('#audioplayer .topbar .title').html(data.title)
  $('#audioplayer .topbar .url.download').attr('href', data.url)
  $('#audioplayer .topbar .url.edit').attr('href', data.urlEdit)

  wavesurfer.load(data.url)

  // Remove old regions
  wavesurfer.clearRegions()
  $('#audioplayer .toolbar .regions').html('')

  // Add new regions
  for (region of data.regions) {
    // console.log(region)
    wavesurfer.addRegion(region)
    const button = '<a class="button" onclick="playRegion(\'' + region.id + '\')">' + region.attributes.title + '</a>'
    $('#audioplayer .toolbar .regions').append(button)
  }
}

function playRegion (regionId) {
  let region = wavesurfer.regions.list[regionId]

  if ($('#loop').is(':checked')) {
    region.setLoop(true)
  } else {
    region.setLoop(false)
  }

  region.play()
}

function updateFileRegions () {
  if ($('#audioplayer').attr('data-file-dirty')) {
    if (confirm('Save regions?')) {
      let fileId = $('#audioplayer').data('file-id')
      let regions = wavesurfer.regions.list
      let regionData = []
      for (let index in regions) {
        let region = regions[index]
        regionData.push({
          id: region.id,
          start: region.start,
          end: region.end,
          attributes: {
            title: region.attributes.title
          }
        })
      }
      regionData = JSON.stringify(regionData)
      postEditFile(fileId, {regions: regionData}, function() {
        $('#audioplayer').removeAttr('data-file-dirty')
      })
    } else {
      $('#audioplayer').removeAttr('data-file-dirty')
    }
  }
}

function initAudioplayer () {
  const audioTags = jQuery('.audio-player audio')
  audioTags.bind('ended', function () {
    current = jQuery(this).closest('.item-audio')
    next = current.nextAll('.item-audio').find('audio')
    if (next[0]) {
      console.log('Playing next song.')
      next[0].play()
    } else {
      console.log('No further songs to play.')
    }
  })
}

function initDatefilters () {
  $('.button[data-status]').bind('click', function (event) {
    event.preventDefault()
    el = $(event.target)

    if (el.hasClass('active')) {
      el.removeClass('active')
    } else {
      el.addClass('active')
    }
    filterDates()
  })
}

function filterDates () {
  allStateToggles = $('[data-status]')

  if (allStateToggles.hasClass('active')) {
    allStateToggles.each(function () {
      toggle = $(this)
      status = toggle.data('status')
      if (toggle.hasClass('active')) {
        $('table.dates tr.status' + status).show()
      } else {
        $('table.dates tr.status' + status).hide()
      }
    })
  } else {
    $('table.dates tr.status' + status).show()
  }
}

function postEditFile (fileId, data, callback) {
  $.ajax({
    type: 'POST',
    url: urlFileEdit + '/' + fileId,
    headers: {
      'X-CSRF-Token': csrfToken
    },
    data,
    success: function (data, textStatus, jqXHR) {
      if (jqXHR.status == 200) {
        // console.dir('Data: ' + data);
        // console.dir(jqXHR);
      }
      callback()
    },
    dataType: 'json'
  })
}
