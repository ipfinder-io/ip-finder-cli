class IpFinderCli < Formula
  desc "The official command command-line for IPFinder"
  homepage "https://ipfinder.io/"
  url "https://github.com/ipfinder-io/ip-finder-cli/releases/download/v1.0.2/ipfinder.phar"
  sha256 "9c99ff6d75293cd9fb0efb9bbd9e0e5c6212067c23e402e5133d5e4ad093cded"
  def install
    bin.install "ipfinder.phar" => "ipfinder"
  end
  test do
    assert_match /IPFinder Command Line Interface 1.0.2 by ipfinder.io Teams/, shell_output("#{bin}/ipfinder --version")
  end
end
