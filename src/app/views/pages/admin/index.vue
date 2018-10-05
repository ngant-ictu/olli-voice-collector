<template>
  <transition>
    <div class="panel-body">
      <p>
        You logged in as <strong>{{ loggedUser.fullname }}</strong>
      </p>
    </div>
  </transition>
</template>

<script lang="ts">
import { Vue, Component } from 'nuxt-property-decorator';
import { State } from 'vuex-class';
import { TimelineMax, TweenMax, Power4 } from 'gsap';

@Component({
  layout: 'admin',
  middleware: ['authenticated']
})
export default class AdminPage extends Vue {
  @State(state => state.authUser.sub) loggedUser;

  transition() {
    return {
      enter: (el, done) => {
        const tl = new TimelineMax({
  				onComplete: done
  			})

  			tl.set(el, {
  				y: window.innerWidth * 1.5,
  				scale: 1,
  				transformOrigin: '50% 50%'
  			})

  			tl.to(el, 0.5, {
  				y: 0,
  				ease: Power4.easeOut
  			});
      }
    };
  }
}
</script>
