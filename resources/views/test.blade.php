<style>
  img{
    width: 100px;
    animation: rotates 2s linear infinite;
  }
  @keyframes rotates {
    0%{
      transform: rotate3d(0,1,0,0deg) translateY(-5px);
    }
    50%{
      transform: rotate3d(0,1,0,180deg) translateY(-10px);

    }
    100%{
      transform: rotate3d(0,1,0,0deg) translateY(-5px);
    }
  }
</style>

<img src="{{url('images/loader.svg')}}" alt="">
