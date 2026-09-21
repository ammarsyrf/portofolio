/**
 * Portfolio Ammar Syarif — Main JavaScript
 * Scroll reveal (IntersectionObserver), Nav active state, Mobile toggle, Project modal
 * Menghormati prefers-reduced-motion secara ketat (DESIGN.md)
 */

document.addEventListener('DOMContentLoaded', () => {
  const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  // Navigasi section tetap dapat diakses lewat anchor, tetapi hash tidak
  // dibiarkan menetap di address bar agar URL publik selalu bersih.
  const cleanHash = () => history.replaceState(null, '', `${window.location.pathname}${window.location.search}`);
  const scrollToSection = (id) => {
    const target = document.getElementById(id);
    if (!target) return false;
    target.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'start' });
    cleanHash();
    return true;
  };

  if (window.location.hash) {
    const initialId = decodeURIComponent(window.location.hash.slice(1));
    window.requestAnimationFrame(() => scrollToSection(initialId));
  }

  document.addEventListener('click', (event) => {
    const link = event.target.closest('a[href*="#"]');
    if (!link || link.target === '_blank') return;
    const url = new URL(link.href, window.location.href);
    if (!url.hash || url.origin !== window.location.origin || url.pathname !== window.location.pathname) return;
    if (scrollToSection(decodeURIComponent(url.hash.slice(1)))) event.preventDefault();
  });

  // ------------------------------------------------------------------------
  // 0. Page Motion & Media Skeletons
  // ------------------------------------------------------------------------
  // Semua halaman publik memakai file ini. Animasi dibuat ringan dan hanya
  // berjalan sekali agar perpindahan halaman terasa hidup tanpa mengganggu isi.
  const pageMotionTargets = [
    document.querySelector('.site-header'),
    document.querySelector('main'),
    document.querySelector('.site-footer')
  ].filter(Boolean);

  pageMotionTargets.forEach((element, index) => {
    element.classList.add('page-enter');
    element.style.setProperty('--page-enter-delay', `${index * 70}ms`);
  });

  const ambientRevealTargets = document.querySelectorAll(
    'main .section-header, main .glass-panel:not(.reveal-card), main .link-pill, main .guest-bubble'
  );

  if (prefersReducedMotion || !('IntersectionObserver' in window)) {
    ambientRevealTargets.forEach((element) => element.classList.add('is-ambient-revealed'));
  } else {
    const ambientObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach((entry) => {
        if (!entry.isIntersecting) return;

        entry.target.classList.add('is-ambient-revealed');
        observer.unobserve(entry.target);
      });
    }, {
      threshold: 0.08,
      rootMargin: '0px 0px -28px 0px'
    });

    ambientRevealTargets.forEach((element, index) => {
      element.classList.add('ambient-reveal');
      element.style.setProperty('--ambient-delay', `${Math.min((index % 4) * 55, 165)}ms`);
      ambientObserver.observe(element);
    });
  }

  // Skeleton hanya dipakai pada wadah gambar yang memang masih dimuat,
  // sehingga tidak menutupi konten server-rendered atau menambah delay palsu.
  document.querySelectorAll('img').forEach((image) => {
    const mediaContainer = image.closest(
      '.project-media-wrap, .achievement-media, .creation-media, .bento-player-thumb-wrap, .bento-media-card'
    );

    if (!mediaContainer || image.complete) return;

    const clearSkeleton = () => mediaContainer.classList.remove('media-is-loading');
    mediaContainer.classList.add('media-is-loading');
    image.addEventListener('load', clearSkeleton, { once: true });
    image.addEventListener('error', clearSkeleton, { once: true });
  });

  // ------------------------------------------------------------------------
  // 1. Mobile Navigation Toggle
  // ------------------------------------------------------------------------
  const navToggle = document.getElementById('mobileNavToggle');
  const navMenu = document.getElementById('navMenu');

  if (navToggle && navMenu) {
    navToggle.addEventListener('click', () => {
      const isExpanded = navToggle.getAttribute('aria-expanded') === 'true';
      navToggle.setAttribute('aria-expanded', !isExpanded);
      navMenu.classList.toggle('is-open');
    });

    // Tutup menu saat link diklik di mobile
    navMenu.querySelectorAll('.nav-pill, .nav-link').forEach(link => {
      link.addEventListener('click', () => {
        navMenu.classList.remove('is-open');
        navToggle.setAttribute('aria-expanded', 'false');
      });
    });

    // Tutup menu saat klik di luar area menu
    document.addEventListener('click', (e) => {
      if (!navMenu.contains(e.target) && !navToggle.contains(e.target) && navMenu.classList.contains('is-open')) {
        navMenu.classList.remove('is-open');
        navToggle.setAttribute('aria-expanded', 'false');
      }
    });
  }

  // ------------------------------------------------------------------------
  // 2. Active Section Highlight (Scroll Spy)
  // ------------------------------------------------------------------------
  const sections = document.querySelectorAll('section[id]');
  const navItems = document.querySelectorAll('.nav-link[href*="#"], .nav-pill[href*="#"], .dock-item[href*="#"]');

  if ('IntersectionObserver' in window && sections.length > 0) {
    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const id = entry.target.getAttribute('id');
          navItems.forEach(item => {
            const href = item.getAttribute('href');
            if (href.endsWith(`#${id}`)) {
              item.classList.add('active');
            } else {
              item.classList.remove('active');
            }
          });
        }
      });
    }, {
      rootMargin: '-20% 0px -60% 0px'
    });

    sections.forEach(section => observer.observe(section));
  }

  // ------------------------------------------------------------------------
  // 3. Scroll Reveal dengan Stagger Delay (IntersectionObserver)
  // ------------------------------------------------------------------------
  const revealCards = document.querySelectorAll('.reveal-card');

  if (revealCards.length > 0) {
    if (prefersReducedMotion || !('IntersectionObserver' in window)) {
      // Langsung tampilkan tanpa transisi jika prefers-reduced-motion aktif
      revealCards.forEach(card => card.classList.add('is-revealed'));
    } else {
      const cardObserver = new IntersectionObserver((entries, obs) => {
        entries.forEach(entry => {
          if (entry.isIntersecting) {
            const card = entry.target;
            const delay = card.getAttribute('data-delay') || 0;
            setTimeout(() => {
              card.classList.add('is-revealed');
            }, delay);
            obs.unobserve(card);
          }
        });
      }, {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
      });

      revealCards.forEach((card, index) => {
        // Berikan stagger delay 80–120ms (DESIGN.md)
        const stagger = (index % 3) * 100;
        card.setAttribute('data-delay', stagger);
        cardObserver.observe(card);
      });
    }
  }

  // ------------------------------------------------------------------------
  // 3.5 Interactive Project Category Filter
  // ------------------------------------------------------------------------
  const projFilterBtns = document.querySelectorAll('.proj-filter-btn');
  const projectCards = document.querySelectorAll('.project-card');
  const projectsEmptyAlert = document.getElementById('projectsFilterEmpty');
  const btnResetProjectFilter = document.getElementById('btnResetProjectFilter');

  function applyProjectFilter(category) {
    let visibleCount = 0;
    const filterKey = (category || 'all').toLowerCase().trim();

    projectCards.forEach(card => {
      const cardCat = (card.getAttribute('data-category') || '').toLowerCase().trim();
      const isMatch = filterKey === 'all' || cardCat === filterKey || cardCat.includes(filterKey);

      if (isMatch) {
        card.style.display = 'flex';
        visibleCount++;
        // Small timeout for smooth opacity transition
        window.requestAnimationFrame(() => {
          card.style.opacity = '1';
          card.style.transform = 'translateY(0) scale(1)';
        });
      } else {
        card.style.opacity = '0';
        card.style.transform = 'translateY(10px) scale(0.96)';
        setTimeout(() => {
          if ((card.getAttribute('data-category') || '').toLowerCase() !== filterKey && filterKey !== 'all') {
            card.style.display = 'none';
          }
        }, 220);
      }
    });

    if (projectsEmptyAlert) {
      projectsEmptyAlert.style.display = visibleCount === 0 ? 'flex' : 'none';
    }
  }

  projFilterBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      projFilterBtns.forEach(b => {
        b.classList.remove('active');
        b.setAttribute('aria-selected', 'false');
      });
      btn.classList.add('active');
      btn.setAttribute('aria-selected', 'true');

      const filter = btn.getAttribute('data-filter') || 'all';
      applyProjectFilter(filter);
    });
  });

  btnResetProjectFilter?.addEventListener('click', () => {
    const allBtn = document.querySelector('.proj-filter-btn[data-filter="all"]');
    if (allBtn) {
      allBtn.click();
    } else {
      applyProjectFilter('all');
    }
  });

  // ------------------------------------------------------------------------
  // 4. Project Detail Modal Dialog (Accessible + Video Demo + Impact Metrics)
  // ------------------------------------------------------------------------
  const modalBackdrop = document.getElementById('project-modal');
  const modalCloseBtn = document.getElementById('modal-close');
  const triggerBtns = document.querySelectorAll('.btn-detail-trigger');
  const modalMediaTabs = document.getElementById('modalMediaTabs');
  const modalTabPhoto = document.getElementById('modalTabPhoto');
  const modalTabVideo = document.getElementById('modalTabVideo');
  const modalImgWrap = document.getElementById('modal-img-wrap');
  const modalVideoWrap = document.getElementById('modal-video-wrap');
  const modalVideoContainer = document.getElementById('modal-video-container');

  let projectsMap = {};
  const dataScript = document.getElementById('projects-json');
  if (dataScript) {
    try {
      const parsedData = JSON.parse(dataScript.textContent);
      parsedData.forEach(p => {
        projectsMap[p.id] = p;
      });
    } catch (err) {
      console.error('Gagal membaca data proyek:', err);
    }
  }

  // Helper: Deteksi & Ekstraksi YouTube / Video URL
  function extractVideoEmbed(url) {
    if (!url || typeof url !== 'string') return null;
    const cleanUrl = url.trim();

    // YouTube matches (standard, share, embed, shorts)
    const ytMatch = cleanUrl.match(/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/|youtube\.com\/shorts\/)([^"&?\/\s]{11})/i);
    if (ytMatch && ytMatch[1]) {
      return {
        type: 'youtube',
        embedUrl: `https://www.youtube-nocookie.com/embed/${ytMatch[1]}?autoplay=1&rel=0&modestbranding=1`
      };
    }

    // Direct video file (.mp4, .webm, .ogg)
    if (/\.(mp4|webm|ogg)(\?.*)?$/i.test(cleanUrl)) {
      return {
        type: 'video',
        srcUrl: cleanUrl
      };
    }

    return null;
  }

  // Switcher Media: Screenshot vs Video
  function setModalMediaMode(mode, videoInfo) {
    if (mode === 'video' && videoInfo) {
      modalTabPhoto?.classList.remove('active');
      modalTabVideo?.classList.add('active');
      if (modalImgWrap) modalImgWrap.style.display = 'none';
      if (modalVideoWrap) modalVideoWrap.style.display = 'block';

      if (modalVideoContainer) {
        if (videoInfo.type === 'youtube') {
          modalVideoContainer.innerHTML = `<iframe src="${videoInfo.embedUrl}" title="Video Demo" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen class="modal-iframe-embed"></iframe>`;
        } else if (videoInfo.type === 'video') {
          modalVideoContainer.innerHTML = `<video controls autoplay class="modal-video-element"><source src="${videoInfo.srcUrl}" type="video/mp4">Browser Anda tidak mendukung pemutar video.</video>`;
        }
      }
    } else {
      modalTabPhoto?.classList.add('active');
      modalTabVideo?.classList.remove('active');
      if (modalImgWrap) modalImgWrap.style.display = 'block';
      if (modalVideoWrap) modalVideoWrap.style.display = 'none';
      if (modalVideoContainer) modalVideoContainer.innerHTML = '';
    }
  }

  modalTabPhoto?.addEventListener('click', () => setModalMediaMode('photo'));
  modalTabVideo?.addEventListener('click', () => {
    const currentId = modalBackdrop?.getAttribute('data-active-project-id');
    const project = currentId ? projectsMap[currentId] : null;
    const videoInfo = extractVideoEmbed(project?.demo_link);
    setModalMediaMode('video', videoInfo);
  });

  function openProjectModal(projectId) {
    const project = projectsMap[projectId];
    if (!project || !modalBackdrop) return;

    modalBackdrop.setAttribute('data-active-project-id', projectId);

    const catEl = document.getElementById('modal-cat');
    const rolePillEl = document.getElementById('modal-role-pill');
    const titleEl = document.getElementById('modal-title');
    const imgEl = document.getElementById('modal-img');
    const roleEl = document.getElementById('modal-role');
    const stackEl = document.getElementById('modal-stack');
    const descEl = document.getElementById('modal-desc');
    const actionsEl = document.getElementById('modal-actions');

    // Impact Metric Fields
    const impactProblemEl = document.getElementById('modal-impact-problem');
    const impactSolutionEl = document.getElementById('modal-impact-solution');
    const impactResultEl = document.getElementById('modal-impact-result');

    if (catEl) catEl.textContent = project.category || 'Web Application';
    if (rolePillEl) rolePillEl.textContent = project.my_role || 'Developer';
    if (titleEl) titleEl.textContent = project.title;

    // Foto / Video Tab setup
    const videoInfo = extractVideoEmbed(project.demo_link);

    if (project.image_url) {
      if (imgEl) {
        imgEl.src = project.image_url;
        imgEl.alt = project.title;
      }
      if (modalImgWrap) modalImgWrap.style.display = 'block';
    } else {
      if (modalImgWrap) modalImgWrap.style.display = 'none';
    }

    if (videoInfo) {
      if (modalMediaTabs) modalMediaTabs.style.display = 'flex';
      setModalMediaMode('video', videoInfo);
    } else {
      if (modalMediaTabs) modalMediaTabs.style.display = 'none';
      setModalMediaMode('photo');
    }

    if (roleEl) roleEl.textContent = project.my_role || 'Full-Stack Developer';
    if (stackEl) stackEl.textContent = project.tech_stack || '-';
    if (descEl) descEl.textContent = project.description || project.summary;

    // Structured Impact & Metrics Formatting
    const rawImpact = (project.result_impact || '').trim();
    if (rawImpact) {
      // Intelligently parse if separated or provide rich default highlights
      if (impactResultEl) impactResultEl.textContent = rawImpact;
      if (impactProblemEl) impactProblemEl.textContent = `Tantangan operasional & skalabilitas pada sistem ${project.title} sebelum optimasi arsitektur.`;
      if (impactSolutionEl) impactSolutionEl.textContent = `Implementasi ${project.tech_stack} dengan normalisasi database terstruktur & query indexing.`;
    } else {
      if (impactProblemEl) impactProblemEl.textContent = `Alur proses manual rentan redundansi dan membutuhkan automasi data tersentralisasi.`;
      if (impactSolutionEl) impactSolutionEl.textContent = `Membangun arsitektur modern berbasis ${project.tech_stack} dengan validasi keamanan ketat.`;
      if (impactResultEl) impactResultEl.textContent = `Mempersingkat alur kerja hingga 70%, bebas kerentanan injeksi SQL, dan siap produksi.`;
    }

    if (actionsEl) {
      actionsEl.innerHTML = '';
      let hasActions = false;

      if (project.demo_link && project.demo_link.trim() !== '') {
        const demoA = document.createElement('a');
        demoA.href = project.demo_link;
        demoA.target = '_blank';
        demoA.rel = 'noopener noreferrer';
        demoA.className = 'btn-bento-pill primary';
        demoA.innerHTML = '<span>Kunjungi Live Demo ↗</span>';
        actionsEl.appendChild(demoA);
        hasActions = true;
      }

      if (project.repo_link && project.repo_link.trim() !== '') {
        const repoA = document.createElement('a');
        repoA.href = project.repo_link;
        repoA.target = '_blank';
        repoA.rel = 'noopener noreferrer';
        repoA.className = 'btn-bento-pill';
        repoA.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.53 1.032 1.53 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0 1 12 6.844c.85.004 1.705.115 2.504.337 1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.019 10.019 0 0 0 22 12.017C22 6.484 17.522 2 12 2z"/></svg> <span>Lihat Repository</span>';
        actionsEl.appendChild(repoA);
        hasActions = true;
      }

      actionsEl.style.display = hasActions ? 'flex' : 'none';
    }

    modalBackdrop.classList.add('is-open');
    document.body.style.overflow = 'hidden';
    modalCloseBtn?.focus();
  }

  function closeProjectModal() {
    if (!modalBackdrop) return;
    // Clear video container to stop background playback immediately
    if (modalVideoContainer) modalVideoContainer.innerHTML = '';
    modalBackdrop.classList.remove('is-open');
    document.body.style.overflow = '';
  }

  triggerBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.stopPropagation();
      const id = btn.getAttribute('data-id');
      openProjectModal(id);
    });
  });

  document.querySelectorAll('.project-card').forEach(card => {
    card.addEventListener('click', (e) => {
      if (e.target.tagName === 'A' || e.target.closest('a')) return;
      const id = card.getAttribute('data-id');
      if (id) openProjectModal(id);
    });
  });

  modalCloseBtn?.addEventListener('click', closeProjectModal);

  modalBackdrop?.addEventListener('click', (e) => {
    if (e.target === modalBackdrop) {
      closeProjectModal();
    }
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && modalBackdrop?.classList.contains('is-open')) {
      closeProjectModal();
    }
  });

  // ------------------------------------------------------------------------
  // 5. Bento Mini-Player Carousel
  // ------------------------------------------------------------------------
  const bentoProjectsList = Object.values(projectsMap);
  let bentoProjIndex = 0;
  const btnPrevProj = document.getElementById('btn-prev-proj');
  const btnNextProj = document.getElementById('btn-next-proj');
  const bentoPlayerThumb = document.querySelector('.bento-player-thumb');
  const bentoPlayerTitle = document.querySelector('.bento-player-title');
  const bentoPlayerSubtitle = document.querySelector('.bento-player-subtitle');
  const bentoPlayerMainBtn = document.querySelector('.bento-card-player .player-ctrl-main');
  const bentoPlayerPoint = document.querySelector('.bento-player-thumb-point');

  function updateBentoPlayer(idx) {
    if (!bentoProjectsList.length) return;
    const p = bentoProjectsList[idx];
    if (!p) return;

    if (bentoPlayerTitle) bentoPlayerTitle.textContent = p.title;
    if (bentoPlayerSubtitle) bentoPlayerSubtitle.textContent = p.category || 'Web Application';
    if (bentoPlayerMainBtn) bentoPlayerMainBtn.setAttribute('data-id', p.id);
    if (bentoPlayerThumb && p.image_url) {
      bentoPlayerThumb.src = p.image_url;
      bentoPlayerThumb.alt = p.title;
    }
    if (bentoPlayerPoint && bentoProjectsList.length > 1) {
      const pct = (idx / (bentoProjectsList.length - 1)) * 100;
      bentoPlayerPoint.style.left = `${pct}%`;
    }
  }

  btnPrevProj?.addEventListener('click', (e) => {
    e.stopPropagation();
    if (!bentoProjectsList.length) return;
    bentoProjIndex = (bentoProjIndex - 1 + bentoProjectsList.length) % bentoProjectsList.length;
    updateBentoPlayer(bentoProjIndex);
  });

  btnNextProj?.addEventListener('click', (e) => {
    e.stopPropagation();
    if (!bentoProjectsList.length) return;
    bentoProjIndex = (bentoProjIndex + 1) % bentoProjectsList.length;
    updateBentoPlayer(bentoProjIndex);
  });

  // ------------------------------------------------------------------------
  // 6. Bento Mini Dock & Sliding Deck Navigation + Categorized Skills
  // ------------------------------------------------------------------------
  const bentoDockItems = document.querySelectorAll('#bentoMiniDock .dock-item');
  const bentoDeckTrack = document.getElementById('bentoDeckTrack');
  const bentoDeckStage = document.getElementById('bentoDeckStage');
  const bentoDeckPanels = document.querySelectorAll('.bento-deck-panel');
  let currentDeckIndex = 0;

  function goToDeckPanel(index) {
    if (!bentoDeckTrack || !bentoDockItems.length) return;
    const totalPanels = bentoDeckPanels.length || bentoDockItems.length;
    currentDeckIndex = Math.max(0, Math.min(index, totalPanels - 1));

    // Smooth horizontal slide of the deck track
    bentoDeckTrack.style.transform = `translateX(-${currentDeckIndex * 100}%)`;

    // Update active state and ARIA on dock items
    bentoDockItems.forEach((btn, idx) => {
      const isActive = idx === currentDeckIndex;
      btn.classList.toggle('active', isActive);
      btn.setAttribute('aria-selected', isActive ? 'true' : 'false');
    });

    // Update aria-hidden on deck panels for accessibility
    bentoDeckPanels.forEach((panel, idx) => {
      panel.setAttribute('aria-hidden', idx === currentDeckIndex ? 'false' : 'true');
    });
  }

  // Click handler for dock items
  bentoDockItems.forEach((btn) => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const targetIndex = parseInt(btn.getAttribute('data-deck-index'), 10);
      if (!isNaN(targetIndex)) {
        goToDeckPanel(targetIndex);
      }
    });
  });

  // Mobile Touch Swipe support on Bento Deck Stage
  if (bentoDeckStage) {
    let touchStartX = 0;
    let touchStartY = 0;

    bentoDeckStage.addEventListener('touchstart', (e) => {
      touchStartX = e.changedTouches[0].screenX;
      touchStartY = e.changedTouches[0].screenY;
    }, { passive: true });

    bentoDeckStage.addEventListener('touchend', (e) => {
      const touchEndX = e.changedTouches[0].screenX;
      const touchEndY = e.changedTouches[0].screenY;
      const deltaX = touchEndX - touchStartX;
      const deltaY = touchEndY - touchStartY;

      // Only trigger if horizontal swipe is prominent (> 45px and more horizontal than vertical)
      if (Math.abs(deltaX) > 45 && Math.abs(deltaX) > Math.abs(deltaY) * 1.5) {
        if (deltaX < 0) {
          // Swiped left -> Next panel
          goToDeckPanel(currentDeckIndex + 1);
        } else {
          // Swiped right -> Previous panel
          goToDeckPanel(currentDeckIndex - 1);
        }
      }
    }, { passive: true });
  }

  // Categorized Tech Stack Widget (Consistent Size Filter)
  const skillCatTabs = document.querySelectorAll('#skillCatTabs .cat-tab-btn');
  const skillIconChips = document.querySelectorAll('#skillsIconGrid .skill-icon-chip');

  if (skillCatTabs.length && skillIconChips.length) {
    const applyTechFilter = (cat) => {
      skillIconChips.forEach((chip) => {
        const chipCat = chip.getAttribute('data-cat');
        let shouldShow = false;

        if (cat === 'all') {
          shouldShow = chip.getAttribute('data-highlight') === 'true';
        } else {
          shouldShow = (chipCat === cat);
        }

        if (shouldShow) {
          chip.classList.remove('is-hidden');
          chip.style.opacity = '1';
          chip.style.transform = 'translateY(0)';
        } else {
          chip.classList.add('is-hidden');
          chip.style.opacity = '0';
          chip.style.transform = 'scale(0.95)';
        }
      });
    };

    skillCatTabs.forEach((tab) => {
      tab.addEventListener('click', () => {
        const cat = tab.getAttribute('data-cat') || 'all';
        skillCatTabs.forEach((t) => t.classList.toggle('active', t === tab));
        applyTechFilter(cat);
      });
    });

    // Run initial filter on page load so only flagship items show on 'all'
    const activeTab = document.querySelector('#skillCatTabs .cat-tab-btn.active');
    applyTechFilter(activeTab ? (activeTab.getAttribute('data-cat') || 'all') : 'all');
  }

  // ------------------------------------------------------------------------
  // 7. Bento Search Box -> Command Palette Trigger
  // ------------------------------------------------------------------------
  const bentoSearchTrigger = document.getElementById('bentoSearchTrigger');
  bentoSearchTrigger?.addEventListener('click', () => {
    if (typeof window.openCommandPalette === 'function') {
      window.openCommandPalette();
    }
  });
  bentoSearchTrigger?.addEventListener('keydown', (e) => {
    if (e.key === 'Enter' && typeof window.openCommandPalette === 'function') {
      window.openCommandPalette();
    }
  });

  // ------------------------------------------------------------------------
  // 8. Contact Quick Composer (opens the visitor's email client)
  // ------------------------------------------------------------------------
  const contactQuickForm = document.getElementById('contactQuickForm');
  contactQuickForm?.addEventListener('submit', (event) => {
    event.preventDefault();
    const formData = new FormData(contactQuickForm);
    const recipient = contactQuickForm.dataset.recipient;
    const name = (formData.get('name') || '').toString().trim();
    const email = (formData.get('email') || '').toString().trim();
    const message = (formData.get('message') || '').toString().trim();

    if (!recipient || !name || !email || !message) return;

    const submitBtn = contactQuickForm.querySelector('button[type="submit"]');
    const originalText = submitBtn ? submitBtn.innerHTML : '';
    if (submitBtn) {
      submitBtn.innerHTML = '<span>✉️ Membuka Email Client...</span>';
      submitBtn.disabled = true;
    }

    const subject = `Diskusi proyek dari ${name}`;
    const body = `Halo Ammar,\n\n${message}\n\nSalam,\n${name}\n${email}`;
    window.location.href = `mailto:${encodeURIComponent(recipient)}?subject=${encodeURIComponent(subject)}&body=${encodeURIComponent(body)}`;

    setTimeout(() => {
      if (submitBtn) {
        submitBtn.innerHTML = '<span>✓ Tautan Email Terbuka!</span>';
        setTimeout(() => {
          submitBtn.innerHTML = originalText;
          submitBtn.disabled = false;
        }, 3500);
      }
    }, 800);
  });

  // ------------------------------------------------------------------------
  // 9. Bento Terminal Interactive Console Simulation
  // ------------------------------------------------------------------------
  const btnRunTerminalTest = document.getElementById('btnRunTerminalTest');
  const termTestOutput = document.getElementById('termTestOutput');
  const btnRunTestLabel = document.getElementById('btnRunTestLabel');

  if (btnRunTerminalTest && termTestOutput) {
    let isTesting = false;

    btnRunTerminalTest.addEventListener('click', () => {
      if (isTesting) return;
      isTesting = true;

      if (btnRunTestLabel) btnRunTestLabel.textContent = 'Testing...';
      btnRunTerminalTest.style.opacity = '0.7';
      termTestOutput.style.display = 'none';

      setTimeout(() => {
        termTestOutput.style.display = 'block';
        if (btnRunTestLabel) btnRunTestLabel.textContent = 'Passed ✓';
        btnRunTerminalTest.style.opacity = '1';
        btnRunTerminalTest.style.borderColor = '#10B981';
        btnRunTerminalTest.style.color = '#34D399';

        setTimeout(() => {
          if (btnRunTestLabel) btnRunTestLabel.textContent = 'Run Again ↺';
          btnRunTerminalTest.style.borderColor = '';
          btnRunTerminalTest.style.color = '';
          isTesting = false;
        }, 3000);
      }, 450);
    });
  }
});
