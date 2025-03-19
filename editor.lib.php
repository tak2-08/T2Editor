<?php
if (!defined('_GNUBOARD_')) exit;

function _t2e_get_hash($s) { return hash('sha256', $s); }
function _t2e_x($s) { return base64_decode($s); }
function _t2e_enc($d) { return base64_encode($d); }

$_t2e_v = array(
    _t2e_x('VDJFZGl0b3IgTGljZW5zZQ=='),
    _t2e_x('VmVyc2lvbjogMS4w'),
    _t2e_x('Q29weXJpZ2h0IChjKSAyMDI1IFRhazIgKGRzY2x1Yi5rcik=')
);

$_t2e_h = array(
    _t2e_x('MzE5MzIxMzIxMzEyMw=='),
    _t2e_x('OTg3NDMyMTMyMTMy'),
    _t2e_x('NTQzMjE0MzIxMzIx')
);

function _t2e_check_integrity($c, $k) {
    global $_t2e_v, $_t2e_h;
    $v = $_t2e_v;
    $h = $_t2e_h;
    return strpos($c, $v[$k]) !== false;
}

function _t2e_validate_file($f) {
    if (!file_exists($f)) return false;
    $c = file_get_contents($f);
    for ($i = 0; $i < 3; $i++) {
        if (!_t2e_check_integrity($c, $i)) return false;
    }
    return true;
}

function _t2e_get_status() {
    $p = G5_EDITOR_PATH.'/t2editor/';
    $f1 = $p.'License_en.txt';
    $f2 = $p.'License_ko.txt';
    
    if (!_t2e_validate_file($f1) || !_t2e_validate_file($f2)) {
        return array(false, '라이선스 검증 실패');
    }
    return array(true, '');
}

function editor_html($id, $content, $is_dhtml_editor=true) {
    global $g5, $config;
    static $js = true;

    list($valid, $msg) = _t2e_get_status();
    if (!$valid) {
        return '<div class="alert alert-danger"><strong>Error:</strong> '.$msg.'</div>';
    }

    $editor_url = G5_EDITOR_URL.'/'.$config['cf_editor'];
    $html = "<span class=\"sound_only\">웹에디터 시작</span>";
    
    if ($js) {
        $html .= "\n<link href=\"{$editor_url}/css/t2editor.css\" rel=\"stylesheet\">";
        $html .= "\n<script src=\"{$editor_url}/js/t2editor.js\"></script>";
        $js = false;
    }

    if ($is_dhtml_editor) {
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $escaped_content = str_replace(
            array("\\", "'", "\r", "\n"),
            array("\\\\", "\\'", "\\r", "\\n"),
            $content
        );
        
        ob_start(); ?>
<?php
if (isset($_SERVER['HTTP_USER_AGENT']) && stripos($_SERVER['HTTP_USER_AGENT'], 'Android') !== false) {
    // 안드로이드 환경일 경우, CDN으로 로드, 애플애서만 자체 호스팅 아이콘 보이는 문제 발생
    echo '<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">' . "\n";
    echo '<link href="https://fonts.googleapis.com/icon?family=Material+Icons+Outlined" rel="stylesheet">' . "\n";
}
?>
<?php
// 푸른산타님의 게시판 관리자모드 오류 해결버전
if (!function_exists('get_readme_version')) {
    function get_readme_version() {
        $readme_path = G5_PLUGIN_PATH . '/editor/t2editor/readme.txt';
        if (file_exists($readme_path)) {
            $content = file_get_contents($readme_path);
            if (preg_match('/ver_([0-9.]+)/', $content, $matches)) {
                return $matches[1];
            }
        }
        return 'readme.txt 오류! 재설치 해주세요.';
    }
}
?>
<style>
@font-face {
  font-family: "Material Icons";
  font-style: normal;
  font-weight: 400;
  src: url("<?php echo G5_PLUGIN_URL ?>/editor/t2editor/fonts/material-icons/MaterialIcons-Regular.eot"); /* IE6-8 지원 */
  src: local("Material Icons"),
       url("<?php echo G5_PLUGIN_URL ?>/editor/t2editor/fonts/material-icons/MaterialIcons-Regular.woff2") format("woff2"),
       url("<?php echo G5_PLUGIN_URL ?>/editor/t2editor/fonts/material-icons/MaterialIcons-Regular.woff") format("woff"),
       url("<?php echo G5_PLUGIN_URL ?>/editor/t2editor/fonts/material-icons/MaterialIcons-Regular.ttf") format("truetype");
  font-display: swap;
}
.material-icons {
  font-family: "Material Icons";
  font-weight: normal;
  font-style: normal;
  font-size: 24px;
  display: inline-block;
  line-height: 1;
  text-transform: none;
  letter-spacing: normal;
  word-wrap: normal;
  white-space: nowrap;
  direction: ltr;
  -webkit-font-feature-settings: "liga";
  font-feature-settings: "liga";
  -webkit-font-smoothing: antialiased;
  text-rendering: optimizeLegibility;
  -moz-osx-font-smoothing: grayscale;
}

@font-face {
  font-family: "Material Icons Outlined";
  font-style: normal;
  font-weight: 400;
  src: url("<?php echo G5_PLUGIN_URL ?>/editor/t2editor/fonts/material-icons/MaterialIconsOutlined-Regular.woff2") format("woff2"),
       url("<?php echo G5_PLUGIN_URL ?>/editor/t2editor/fonts/material-icons/MaterialIconsOutlined-Regular.ttf") format("truetype");
  font-display: swap;
}

.material-icons-outlined {
  font-family: "Material Icons Outlined";
  font-weight: normal;
  font-style: normal;
  font-size: 24px;
  line-height: 1;
  letter-spacing: normal;
  text-transform: none;
  display: inline-block;
  white-space: nowrap;
  word-wrap: normal;
  direction: ltr;
  -webkit-font-feature-settings: "liga";
  font-feature-settings: "liga";
  -webkit-font-smoothing: antialiased;
  text-rendering: optimizeLegibility;
}
</style>

<div class="t2-editor-container" id="<?php echo $id ?>_container">
    <div class="t2-toolbar">
        <button class="t2-btn" data-command="undo" disabled>
            <span class="material-icons">undo</span>
        </button>
        <button class="t2-btn" data-command="redo" disabled>
            <span class="material-icons">redo</span>
        </button>
        <button class="t2-btn" data-command="fontSize">
            <span class="material-icons">format_size</span>
        </button>
        <button class="t2-btn" data-command="bold">
            <span class="material-icons">format_bold</span>
        </button>
        <button class="t2-btn" data-command="italic">
            <span class="material-icons">format_italic</span>
        </button>
        <button class="t2-btn" data-command="underline">
            <span class="material-icons">format_underlined</span>
        </button>
        <button class="t2-btn" data-command="strikeThrough">
            <span class="material-icons">format_strikethrough</span>
        </button>
        <button class="t2-btn" data-command="justifyContent">
            <span class="material-icons">format_align_left</span>
        </button>
        <button class="t2-btn" data-command="foreColor">
            <span class="material-icons">format_color_text</span>
        </button>
        <button class="t2-btn" data-command="backColor">
            <span class="material-icons">format_color_fill</span>
        </button>
        <button class="t2-btn" data-command="insertImage">
            <span class="material-icons">image</span>
        </button>
        <button class="t2-btn" data-command="insertYouTube" style="color:#f04f48">
            <span class="material-icons">smart_display</span>
        </button>
        <button class="t2-btn" data-command="attachFile">
            <span class="material-icons">attach_file</span>
        </button>
        <button class="t2-btn" data-command="createLink">
            <span class="material-icons">link</span>
        </button>
        <button class="t2-btn" data-command="insertTable">
            <span class="material-icons-outlined">table_chart</span>
        </button>
        <button class="t2-btn" data-command="insertCodeBlock">
            <span class="material-icons">code</span>
        </button>
    <button class="t2-btn" data-command="exportHTML">
        <span class="material-icons-outlined">ios_share</span>
    </button>
    </div>
    <div class="t2-editor" contenteditable="true" id="<?php echo $id ?>_editor"></div>
    <textarea name="<?php echo $id ?>" id="<?php echo $id ?>" style="display:none;">
<?php echo $content ?>
    </textarea>
    <div class="t2-editor-status">
        <div class="t2-status-left">
            <a href="//dsclub.kr/service/editor">
                <div class="t2-logo">
                    <span class="t2-logo-prefix">T2</span>
                    <span class="t2-logo-suffix">Editor</span>
                </div>
            </a>
        </div>
        <div class="t2-char-count">
            txt: <span>0</span>
        </div>
    </div>
<span style="color: #999; position: absolute; right: 5px; margin:5px 0; font-size: 11px; font-weight: 500; display: flex; align-items: center;">
  <i class="material-icons-outlined" style="margin-right: 4px; font-size: 14px">info</i>
  <?php echo (strpos($v = get_readme_version(), '오류') !== false) ? $v : "T2Editor Ver $v"; ?>
</span>
</div>

<script>
(function() {
    var editor = new T2Editor(document.getElementById('<?php echo $id ?>_container'));
    window.<?php echo $id ?>_editor = editor;
    
    if ('<?php echo $escaped_content ?>') {
        try {
            var contentToLoad = '<?php echo $escaped_content ?>';
            var tempDiv = document.createElement('div');
            tempDiv.innerHTML = contentToLoad;
            
            // 중복 줄바꿈 제거 전처리
            var nodes = Array.from(tempDiv.childNodes);
            for (let i = nodes.length - 1; i >= 0; i--) {
                let current = nodes[i];
                if (current.nodeType === Node.ELEMENT_NODE && current.tagName === 'P' && !current.textContent.trim() && current.querySelector('br')) {
                    let prev = i > 0 ? nodes[i - 1] : null;
                    let next = i < nodes.length - 1 ? nodes[i + 1] : null;
                    
                    if ((prev && prev.classList?.contains('t2-media-block')) || 
                        (next && next.classList?.contains('t2-media-block'))) {
                        current.remove();
                    }
                }
            }
            
            // 테이블 처리 - 기존 table-responsive 구조를 t2-table-wrapper로 변환
            tempDiv.querySelectorAll('.table-responsive').forEach(function(responsiveWrapper) {
                var table = responsiveWrapper.querySelector('table');
                if (table) {
                    // 테이블 클래스 추가
                    if (!table.classList.contains('t2-table')) {
                        table.classList.add('t2-table');
                    }
                    
                    // 테이블 크기 확인
                    var isLargeTable = table.classList.contains('t2-table-large') || 
                                      (table.rows.length > 10 || (table.rows[0] && table.rows[0].cells.length > 10));
                    if (isLargeTable && !table.classList.contains('t2-table-large')) {
                        table.classList.add('t2-table-large');
                    }
                    
                    // 새로운 래퍼 구조 생성
                    var tableWrapper = document.createElement('div');
                    tableWrapper.className = 't2-table-wrapper';
                    tableWrapper.contentEditable = false;
                    
                    responsiveWrapper.parentNode.insertBefore(tableWrapper, responsiveWrapper);
                    
                    if (isLargeTable) {
                        var scrollWrapper = document.createElement('div');
                        scrollWrapper.className = 't2-table-scroll-wrapper';
                        tableWrapper.appendChild(scrollWrapper);
                        scrollWrapper.appendChild(table);
                    } else {
                        tableWrapper.appendChild(table);
                    }
                    
                    // 테이블 컨트롤 추가
                    var tableControls = document.createElement('div');
                    tableControls.className = 't2-table-controls';
                    tableControls.innerHTML = `
                        <div class="t2-table-control-counter">
                            <span class="t2-row-count">${table.rows.length}</span>행 × <span class="t2-col-count">${table.rows[0] ? table.rows[0].cells.length : 0}</span>열
                        </div>
                        <div class="t2-table-control-group">
                            <span>가로셀:</span>
                            <button class="t2-btn t2-table-control-btn" data-action="add-col">
                                <span class="material-icons">add</span>
                            </button>
                            <button class="t2-btn t2-table-control-btn" data-action="remove-col">
                                <span class="material-icons">remove</span>
                            </button>
                        </div>
                        <div class="t2-table-control-group">
                            <span>세로셀:</span>
                            <button class="t2-btn t2-table-control-btn" data-action="add-row">
                                <span class="material-icons">add</span>
                            </button>
                            <button class="t2-btn t2-table-control-btn" data-action="remove-row">
                                <span class="material-icons">remove</span>
                            </button>
                        </div>
                        <button class="t2-btn t2-table-delete-btn" data-action="delete-table">
                            <span class="material-icons">close</span>
                        </button>
                    `;
                    tableWrapper.appendChild(tableControls);
                    
                    responsiveWrapper.remove();
                }
            });
            
            editor.setContent(tempDiv.innerHTML);
            
            setTimeout(function() {
                editor.initializeBlocks();
                editor.normalizeContent();
                
                // 미디어 블록 처리
                var mediaBlocks = editor.editor.querySelectorAll('.t2-media-block');
                mediaBlocks.forEach(function(block) {
                    var container = block.querySelector('div:first-child');
                    var mediaElement = container.querySelector('iframe, video');
                    var editButton = block.querySelector('.edit-url-btn');
                    
                    if (editButton && mediaElement && mediaElement.tagName === 'IFRAME') {
                        var newEditButton = editButton.cloneNode(true);
                        editButton.parentNode.replaceChild(newEditButton, editButton);
                        
                        newEditButton.addEventListener('click', function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            var videoId = mediaElement.src.match(/embed\/([^?]+)/)?.[1];
                            if (videoId) {
                                editor.showVideoUrlEditModal(mediaElement, { type: 'youtube', id: videoId });
                            }
                        });
                    }
                });
                
                // 테이블 초기화
                editor.initializeTableBlocks();
                
                // 테이블 컨트롤 이벤트 설정
                editor.editor.querySelectorAll('.t2-table-wrapper').forEach(function(wrapper) {
                    var table = wrapper.querySelector('table');
                    var controls = wrapper.querySelector('.t2-table-controls');
                    
                    if (table && controls) {
                        var newControls = controls.cloneNode(true);
                        controls.parentNode.replaceChild(newControls, controls);
                        editor.setupTableControlEvents(newControls, table);
                        editor.setupTableCellEditing(table);
                        if (typeof editor.setupTableResizing === 'function') {
                            editor.setupTableResizing(table);
                        }
                    }
                });
            }, 100);
        } catch (e) {
            console.error('에디터 초기화 오류:', e);
        }
    }
})();
</script>
        <?php
        $html .= ob_get_clean();
    } else {
        $html .= "\n<textarea id=\"{$id}\" name=\"{$id}\" style=\"width:100%;height:300px\">{$content}</textarea>";
    }
    
    return $html;
}

function get_editor_js($id, $is_dhtml_editor=true) {
    if ($is_dhtml_editor) {
        return "
var _submitContent = function() {
    var editorContent = document.getElementById('{$id}_editor').innerHTML;
    var tempDiv = document.createElement('div');
    tempDiv.innerHTML = editorContent;
    
    // 파일 블록 처리
    tempDiv.querySelectorAll('.t2-file-block').forEach(function(block) {
        // 컨트롤 제거 (게시글에서는 표시하지 않음)
        const controls = block.querySelector('.t2-media-controls');
        if (controls) {
            controls.remove();
        }
    });
    
    // 미디어 블록 처리
    tempDiv.querySelectorAll('.t2-media-block').forEach(function(block) {
        var container = block.querySelector('div:first-child');
        var mediaElement = container.querySelector('iframe, video, img');
        if (mediaElement) {
            mediaElement.style.width = container.style.width;
            if (container.style.height) {
                mediaElement.style.height = container.style.height;
            }
            
            var controls = block.querySelector('.t2-media-controls');
            if (controls) {
                controls.remove();
            }
        }
    });
    
    // 테이블 래퍼 및 컨트롤 처리 - 수정됨
    tempDiv.querySelectorAll('.t2-table-wrapper').forEach(function(wrapper) {
        const table = wrapper.querySelector('table');
        if (table) {
            // 테이블 컨트롤 제거 (게시글에서는 표시하지 않음)
            const controls = wrapper.querySelector('.t2-table-controls');
            if (controls) {
                controls.remove();
            }
            
            // 큰 테이블인지 확인 (t2-table-large 클래스 또는 10x10 이상)
            const isLargeTable = table.classList.contains('t2-table-large') || 
                               (table.rows.length > 10 || (table.rows[0] && table.rows[0].cells.length > 10));
                               
            // 스크롤 래퍼가 있는지 확인
            const hasScrollWrapper = wrapper.querySelector('.t2-table-scroll-wrapper');
            
            if (isLargeTable || hasScrollWrapper) {
                // 스크롤 가능한 컨테이너를 만들어 테이블 포함
                const scrollContainer = document.createElement('div');
                scrollContainer.className = 'table-responsive';
                scrollContainer.style.cssText = 'display:block; width:100%; overflow-x:auto; -webkit-overflow-scrolling:touch;';
                
                // 원래 래퍼에서 테이블 제거 전 위치 저장
                wrapper.parentNode.insertBefore(scrollContainer, wrapper);
                
                // 테이블이 스크롤 래퍼 안에 있는 경우 먼저 꺼냄
                if (hasScrollWrapper) {
                    hasScrollWrapper.parentNode.insertBefore(table, hasScrollWrapper);
                    hasScrollWrapper.remove();
                }
                
                // 테이블을 스크롤 컨테이너로 이동
                scrollContainer.appendChild(table);
                
                // 기존 래퍼 제거
                wrapper.remove();
            } else {
                // 작은 테이블은 그냥 래퍼에서 꺼냄
                wrapper.parentNode.insertBefore(table, wrapper);
                wrapper.remove();
            }
        }
    });
    
    // 테이블 스크롤 래퍼 처리 (별도 처리 - 래퍼 밖에 있는 경우)
    tempDiv.querySelectorAll('.t2-table-scroll-wrapper').forEach(function(scrollWrapper) {
        const table = scrollWrapper.querySelector('table');
        if (table) {
            const scrollContainer = document.createElement('div');
            scrollContainer.className = 'table-responsive';
            scrollContainer.style.cssText = 'display:block; width:100%; overflow-x:auto; -webkit-overflow-scrolling:touch;';
            
            scrollWrapper.parentNode.insertBefore(scrollContainer, scrollWrapper);
            scrollContainer.appendChild(table);
            scrollWrapper.remove();
        }
    });
    
    // 빈 문단 처리
    tempDiv.querySelectorAll('p').forEach(function(p) {
        if (!p.textContent.trim() && !p.querySelector('img, iframe, video')) {
            if (!p.querySelector('br')) {
                p.innerHTML = '<br>';
            }
        }
    });

    // 스타일 추가 - 스크롤 테이블 스타일 추가
    var customStyle = '<style>' +
        // 미디어 블록 스타일
        '.t2-media-block img, .t2-media-block iframe, .t2-media-block video {' +
            'border-radius: 15px !important; ' +
            'border: none !important; ' +
            'margin: 0 auto !important;' +
        '}' +
        // 파일 블록 스타일
        '.file-container {' +
            'width: 360px; ' +
            'background: white; ' +
            'border-radius: 12px; ' +
            'border: 1px solid #4a4a4a; ' +
            'padding: 20px; ' +
            'display: flex; ' +
            'align-items: center; ' +
            'font-family: Roboto, Arial, sans-serif; ' +
            'margin: 10px 0;' +
        '}' +
        '.file-icon {' +
            'width: 42px; ' +
            'height: 52px; ' +
            'border-radius: 6px; ' +
            'margin-right: 20px; ' +
            'position: relative; ' +
            'flex-shrink: 0; ' +
            'overflow: hidden;' +
        '}' +
        '.file-info {' +
            'flex-grow: 1; ' +
            'min-width: 0;' +
        '}' +
        '.file-name {' +
            'font-size: 17px; ' +
            'font-weight: 500; ' +
            'color: rgba(0,0,0,0.87); ' +
            'margin: 0 0 6px 0; ' +
            'white-space: nowrap; ' +
            'overflow: hidden; ' +
            'text-overflow: ellipsis;' +
        '}' +
        '.file-details {' +
            'color: rgba(0,0,0,0.6); ' +
            'font-size: 14px; ' +
            'line-height: 1.5;' +
        '}' +
        '.file-details span {' +
            'display: inline-block; ' +
            'margin-right: 12px;' +
        '}' +
        // 테이블 스타일
        '.t2-table {' +
            'width: 100%; ' +
            'border-collapse: collapse; ' +
            'margin: 15px 0; ' +
            'font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, sans-serif;' +
        '}' +
        '.t2-table th, .t2-table td {' +
            'border: 1px solid #ccc; ' +
            'padding: 8px; ' +
            'vertical-align: top;' +
        '}' +
        '.t2-table th {' +
            'background-color: #f5f5f5; ' +
            'font-weight: 500;' +
        '}' +
        // 테이블 스크롤 스타일 개선
        '.table-responsive {' +
            'display: block; ' +
            'width: 100%; ' +
            'overflow-x: auto; ' +
            'margin-bottom: 1rem; ' +
            '-webkit-overflow-scrolling: touch;' +
        '}' +
        // 대형 테이블에 최소 너비 적용
        '.t2-table.t2-table-large {' +
            'min-width: 800px;' +
        '}' +
        '</style>';
    
    var finalContent = tempDiv.innerHTML;
    
    // 스타일이 없는 경우에만 추가
    if (finalContent.indexOf('border-radius: 15px') === -1) {
        finalContent += customStyle;
    }
    
    document.getElementById('{$id}').value = finalContent;
};
            _submitContent();\n";
    }
    return "var {$id}_editor = document.getElementById('{$id}');\n";
}

function chk_editor_js($id, $is_dhtml_editor=true) {
    if ($is_dhtml_editor) {
        return "if (!document.getElementById('{$id}_editor').innerHTML.trim()) { alert('내용을 입력해 주십시오.'); document.getElementById('{$id}_editor').focus(); return false; }\n";
    }
    return "if (!{$id}_editor.value) { alert('내용을 입력해 주십시오.'); {$id}_editor.focus(); return false; }\n";
}